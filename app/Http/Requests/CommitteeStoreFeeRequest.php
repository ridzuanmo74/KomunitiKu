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
        $associationId = app(CommitteePortalService::class)
            ->committeeContextAssociation($this->user())
            ?->id;

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('fees', 'name')->where(
                    fn ($query) => $query->where('association_id', $associationId)
                ),
            ],
            'amount' => ['required', 'numeric', 'min:1'],
            'frequency' => ['required', 'string', Rule::in([Fee::FREQUENCY_ONE_TIME, Fee::FREQUENCY_MONTHLY, Fee::FREQUENCY_YEARLY])],
            'due_day' => ['nullable', 'integer', 'between:1,31', 'required_if:frequency,'.Fee::FREQUENCY_MONTHLY],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
