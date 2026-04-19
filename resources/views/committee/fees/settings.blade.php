<x-app-layout>
    <x-slot name="header">
        <h2 class="text-lg font-semibold leading-tight text-gray-800">
            {{ __('Tetapan Yuran') }}
        </h2>
    </x-slot>

    <div class="overflow-hidden rounded-lg border border-gray-200 bg-white">
        <div class="border-b border-gray-200 px-4 py-3 text-sm text-gray-600">
            {{ __('Persatuan') }}: {{ $association?->name ?: __('Tiada') }}
        </div>
        <div class="divide-y divide-gray-100">
            @forelse ($fees as $fee)
                <div class="flex items-center justify-between px-4 py-3">
                    <div>
                        <p class="font-medium text-gray-900">{{ $fee->name }}</p>
                        <p class="text-sm text-gray-600">{{ $fee->due_day ? __('Bulanan') : __('Tahunan') }}</p>
                    </div>
                    <p class="text-sm font-medium text-gray-800">RM {{ number_format((float) $fee->amount, 2) }}</p>
                </div>
            @empty
                <div class="px-4 py-8 text-center text-sm text-gray-500">{{ __('Tiada yuran didaftarkan.') }}</div>
            @endforelse
        </div>
    </div>
</x-app-layout>
