<x-app-layout>
    <x-slot name="header">
        <h2 class="text-lg font-semibold leading-tight text-gray-800">
            {{ __('Kelulusan Keahlian') }}
        </h2>
    </x-slot>

    <div class="rounded-lg border border-amber-200 bg-amber-50 px-4 py-4 text-sm text-amber-800">
        {{ __('Halaman ini disediakan untuk senarai permohonan keahlian yang menunggu semakan jawatankuasa.') }}
        @if ($association)
            <span class="block mt-1">{{ __('Persatuan semasa') }}: {{ $association->name }}</span>
        @endif
    </div>
</x-app-layout>
