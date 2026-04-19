<?php

namespace App\Http\Requests;

use App\Models\Association;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreAssociationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Association::class) ?? false;
    }

    /**
     * @return array<string, array<int, string|ValidationRule>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', 'unique:associations,code'],
            'description' => ['nullable', 'string'],
            'is_active' => ['sometimes', 'boolean'],
            'ros_registration_number' => ['nullable', 'string', 'max:100'],
            'established_date' => ['nullable', 'date'],
            'address' => ['nullable', 'string', 'max:5000'],
            'postcode' => ['nullable', 'string', 'max:20'],
            'city' => ['nullable', 'string', 'max:255'],
            'state_id' => ['nullable', 'integer', 'exists:states,id'],
            'phone' => ['nullable', 'string', 'max:50'],
            'official_email' => ['nullable', 'email', 'max:255'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->normalizeIsActiveFromForm(),
        ]);

        foreach (['latitude', 'longitude'] as $field) {
            if ($this->has($field) && $this->input($field) === '') {
                $this->merge([$field => null]);
            }
        }
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $lat = $validator->getData()['latitude'] ?? null;
            $lng = $validator->getData()['longitude'] ?? null;
            $latPresent = $lat !== null && $lat !== '';
            $lngPresent = $lng !== null && $lng !== '';
            if ($latPresent xor $lngPresent) {
                $validator->errors()->add('latitude', __('Latitud dan longitud mesti kedua-duanya diisi atau dibiarkan kosong.'));
                $validator->errors()->add('longitude', __('Latitud dan longitud mesti kedua-duanya diisi atau dibiarkan kosong.'));
            }
        });
    }

    private function normalizeIsActiveFromForm(): bool
    {
        $v = $this->input('is_active');
        if (is_array($v)) {
            return in_array('1', $v, true) || in_array(1, $v, true);
        }

        return $v === '1' || $v === 1 || $v === true;
    }
}
