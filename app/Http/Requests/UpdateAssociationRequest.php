<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\PreparesAssociationRequestInput;
use App\Models\Association;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateAssociationRequest extends FormRequest
{
    use PreparesAssociationRequestInput;

    public function authorize(): bool
    {
        $association = $this->route('association');

        return $association instanceof Association
            && ($this->user()?->can('update', $association) ?? false);
    }

    /**
     * @return array<string, array<int, string|ValidationRule>|string>
     */
    public function rules(): array
    {
        $association = $this->route('association');
        $associationId = $association instanceof Association ? $association->id : 0;

        return [
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', Rule::unique('associations', 'code')->ignore($associationId)],
            'description' => ['nullable', 'string'],
            'is_active' => ['sometimes', 'boolean'],
            'ros_registration_number' => ['nullable', 'string', 'max:100'],
            'established_date' => ['required', 'date', 'before_or_equal:today'],
            'address' => ['required', 'string', 'min:5', 'max:5000'],
            'postcode' => ['required', 'digits:5'],
            'city' => ['nullable', 'string', 'max:255'],
            'state_id' => ['required', 'integer', 'exists:states,id'],
            'phone' => ['required', 'string', 'max:50', 'regex:'.StoreAssociationRequest::MALAYSIAN_PHONE_REGEX],
            'official_email' => ['required', 'email', 'max:255'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
        ];
    }

    public function messages(): array
    {
        return [
            'phone.regex' => __('Format nombor telefon tidak sah. Gunakan format Malaysia (contoh: 0123456789 atau 03-12345678).'),
            'postcode.digits' => __('Poskod mesti tepat 5 digit nombor.'),
            'established_date.before_or_equal' => __('Tarikh ditubuhkan tidak boleh melebihi hari ini.'),
            'address.min' => __('Sila masukkan alamat yang lengkap.'),
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->mergeNormalizedAssociationPayload();
    }

    public function withValidator(Validator $validator): void
    {
        $this->validateAssociationLatitudeLongitudePair($validator);
    }
}
