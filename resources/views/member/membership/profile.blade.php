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
                    <dd class="mt-1 text-sm text-gray-900">{{ $membership->joined_at ?: '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Status') }}</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $membership->is_active ? __('Aktif') : __('Tidak Aktif') }}</dd>
                </div>
            </dl>
        @else
            <div class="px-4 py-8 text-center text-sm text-gray-500">
                {{ __('Maklumat keahlian tidak tersedia. Sila pilih persatuan aktif dahulu.') }}
            </div>
        @endif
    </div>
</x-app-layout>
