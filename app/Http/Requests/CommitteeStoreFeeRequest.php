<?php

namespace App\Http\Requests;

use App\Models\Fee;
use App\Services\CommitteePortalService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class CommitteeStoreFeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        $associationId = app(CommitteePortalService::class)
            ->committeeContextAssociation($this->user())
            ?->id;

        return $associationId !== null && Gate::allows('create', [Fee::class, $associationId]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'frequency' => ['required', 'string', Rule::in([Fee::FREQUENCY_ONE_TIME, Fee::FREQUENCY_MONTHLY, Fee::FREQUENCY_YEARLY])],
            'due_day' => ['nullable', 'integer', 'between:1,31', 'required_if:frequency,'.Fee::FREQUENCY_MONTHLY],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
