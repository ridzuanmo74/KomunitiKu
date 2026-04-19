@php
    $propertyLabel = static function (?string $v): string {
        return match ($v) {
            'owner' => __('Pemilik'),
            'tenant' => __('Penyewa'),
            'family_member' => __('Ahli keluarga'),
            default => '—',
        };
    };
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="text-lg font-semibold leading-tight text-gray-800">
            {{ __('Profil Keahlian') }}
        </h2>
    </x-slot>

    <div class="overflow-hidden rounded-lg border border-gray-200 bg-white">
        @if ($activeAssociation && $membership)
            <dl class="grid gap-4 p-4 sm:grid-cols-2">
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Persatuan Aktif') }}</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $activeAssociation->name }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('No Ahli') }}</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $membership->membership_no ?: '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Tarikh Sertai') }}</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $membership->joined_at?->format('Y-m-d') ?: '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Status') }}</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $membership->is_active ? __('Aktif') : __('Tidak Aktif') }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Telefon (di persatuan)') }}</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $membership->phone ?: '—' }}</dd>
                </div>
                <div class="sm:col-span-2">
                    <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Alamat (di persatuan)') }}</dt>
                    <dd class="mt-1 whitespace-pre-wrap text-sm text-gray-900">{{ $membership->address ?: '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Poskod') }}</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $membership->postcode ?: '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Bandar') }}</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $membership->city ?: '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Negeri') }}</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $membership->relationLoaded('state') && $membership->state ? $membership->state->name : '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Latitud') }}</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $membership->latitude !== null ? (string) $membership->latitude : '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Longitud') }}</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $membership->longitude !== null ? (string) $membership->longitude : '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Hubungan dengan harta') }}</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $propertyLabel($membership->property_relationship) }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Layak mengundi') }}</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $membership->is_voting_eligible ? __('Ya') : __('Tidak') }}</dd>
                </div>
            </dl>
        @else
            <div class="px-4 py-8 text-center text-sm text-gray-500">
                {{ __('Maklumat keahlian tidak tersedia. Sila pilih persatuan aktif dahulu.') }}
            </div>
        @endif
    </div>
</x-app-layout>
