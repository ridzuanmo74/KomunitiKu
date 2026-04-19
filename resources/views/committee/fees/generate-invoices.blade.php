<x-app-layout>
    <x-slot name="header">
        <h2 class="text-lg font-semibold leading-tight text-gray-800">
            {{ __('Jana Invois Berkala') }}
        </h2>
    </x-slot>

    <div class="rounded-lg border border-blue-200 bg-blue-50 px-4 py-4 text-sm text-blue-800">
        {{ __('Skrin ini disediakan untuk aliran jana invois automatik bulanan/tahunan.') }}
        @if ($association)
            <span class="block mt-1">{{ __('Persatuan semasa') }}: {{ $association->name }}</span>
        @endif
    </div>
</x-app-layout>
