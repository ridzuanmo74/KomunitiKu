<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\PreparesAssociationRequestInput;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAssociationMemberRequest extends FormRequest
{
    use PreparesAssociationRequestInput;

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $merge = [
            'is_voting_eligible' => $this->boolean('is_voting_eligible'),
        ];

        if ($this->has('address') && is_string($this->input('address'))) {
            $merge['address'] = trim($this->input('address'));
        }

        if ($this->has('postcode') && is_string($this->input('postcode'))) {
            $merge['postcode'] = trim($this->input('postcode'));
        }

        foreach (['latitude', 'longitude'] as $key) {
            if ($this->has($key) && $this->input($key) === '') {
                $merge[$key] = null;
            }
        }

        if ($this->has('phone') && is_string($this->input('phone'))) {
            $merge['phone'] = $this->normalizeMalaysianPhone(trim($this->input('phone')));
        }

        $this->merge($merge);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'membership_no' => ['nullable', 'string', 'max:255'],
            'address' => ['required', 'string'],
            'postcode' => ['required', 'string', 'max:20'],
            'city' => ['nullable', 'string', 'max:255'],
            'state_id' => ['required', 'integer', 'exists:states,id'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'property_relationship' => ['required', 'string', Rule::in(['owner', 'tenant', 'family_member'])],
            'is_voting_eligible' => ['boolean'],
            'phone' => ['required', 'string', 'max:50', 'regex:'.StoreAssociationRequest::MALAYSIAN_PHONE_REGEX],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'address' => __('Alamat'),
            'postcode' => __('Poskod'),
            'state_id' => __('Negeri'),
            'property_relationship' => __('Hubungan dengan harta'),
            'phone' => __('Telefon'),
        ];
    }
}
