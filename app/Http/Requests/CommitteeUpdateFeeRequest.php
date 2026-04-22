<?php

namespace App\Http\Requests;

use App\Models\Fee;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class CommitteeUpdateFeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        $fee = $this->route('fee');

        return $fee instanceof Fee && Gate::allows('update', $fee);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $fee = $this->route('fee');
        $associationId = $fee instanceof Fee ? (int) $fee->association_id : null;

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('fees', 'name')
                    ->where(fn ($query) => $query->where('association_id', $associationId))
                    ->ignore($fee),
            ],
            'amount' => ['required', 'numeric', 'min:1'],
            'frequency' => ['required', 'string', Rule::in([Fee::FREQUENCY_ONE_TIME, Fee::FREQUENCY_MONTHLY, Fee::FREQUENCY_YEARLY])],
            'due_day' => ['nullable', 'integer', 'between:1,31', 'required_if:frequency,'.Fee::FREQUENCY_MONTHLY],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
