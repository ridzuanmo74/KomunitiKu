<x-app-layout>
    <x-slot name="header">
        <h2 class="text-lg font-semibold leading-tight text-gray-800">
            {{ __('Kad Ahli / Bukti Keahlian') }}
        </h2>
    </x-slot>

    <div class="mx-auto max-w-lg rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
        @if ($activeAssociation && $membership)
            <p class="text-xs font-semibold uppercase tracking-wide text-indigo-600">{{ __('KomunitiKu') }}</p>
            <h3 class="mt-2 text-xl font-semibold text-gray-900">{{ $activeAssociation->name }}</h3>
            <p class="mt-4 text-sm text-gray-600">{{ __('No Ahli') }}: <span class="font-medium text-gray-900">{{ $membership->membership_no ?: '—' }}</span></p>
            <p class="mt-1 text-sm text-gray-600">{{ __('Tarikh Sertai') }}: <span class="font-medium text-gray-900">{{ $membership->joined_at ?: '—' }}</span></p>
            <p class="mt-1 text-sm text-gray-600">{{ __('Status') }}: <span class="font-medium text-gray-900">{{ $membership->is_active ? __('Aktif') : __('Tidak Aktif') }}</span></p>
        @else
            <p class="text-sm text-gray-500">{{ __('Tiada kad ahli untuk dipaparkan pada masa ini.') }}</p>
        @endif
    </div>
</x-app-layout>
