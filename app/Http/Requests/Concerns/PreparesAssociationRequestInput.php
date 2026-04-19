<?php

namespace App\Http\Requests\Concerns;

use Illuminate\Validation\Validator;

trait PreparesAssociationRequestInput
{
    protected function mergeNormalizedAssociationPayload(): void
    {
        $merge = [
            'is_active' => $this->normalizeIsActiveFromForm(),
        ];

        foreach (['name', 'code', 'description', 'ros_registration_number', 'address', 'city', 'official_email'] as $key) {
            if ($this->has($key) && is_string($this->input($key))) {
                $merge[$key] = trim($this->input($key));
            }
        }

        if ($this->has('postcode') && is_string($this->input('postcode'))) {
            $merge['postcode'] = trim($this->input('postcode'));
        }

        if ($this->has('established_date') && is_string($this->input('established_date'))) {
            $merge['established_date'] = trim($this->input('established_date'));
        }

        if ($this->has('phone') && is_string($this->input('phone'))) {
            $merge['phone'] = $this->normalizeMalaysianPhone(trim($this->input('phone')));
        }

        foreach (['latitude', 'longitude'] as $key) {
            if (! $this->has($key)) {
                continue;
            }
            $v = $this->input($key);
            if ($v === '' || $v === null) {
                $merge[$key] = null;
            } elseif (is_string($v)) {
                $merge[$key] = str_replace(',', '.', trim($v));
            }
        }

        $this->merge($merge);
    }

    public function validateAssociationLatitudeLongitudePair(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $data = $validator->getData();
            $lat = $data['latitude'] ?? null;
            $lng = $data['longitude'] ?? null;
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

    private function normalizeMalaysianPhone(string $phone): string
    {
        $clean = preg_replace('/[^\d+]/', '', $phone) ?? '';
        if ($clean === '') {
            return '';
        }
        if (str_starts_with($clean, '+60')) {
            return '0'.substr($clean, 3);
        }
        if (str_starts_with($clean, '60') && strlen($clean) >= 10) {
            return '0'.substr($clean, 2);
        }

        return $clean;
    }
}
