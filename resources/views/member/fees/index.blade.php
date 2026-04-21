<x-app-layout>
    <x-slot name="header">
        <h2 class="text-lg font-semibold leading-tight text-gray-800">
            {{ __('Jenis Yuran') }}
        </h2>
    </x-slot>

    <div class="overflow-hidden rounded-lg border border-gray-200 bg-white">
        <div class="border-b border-gray-200 px-4 py-3 text-sm text-gray-600">
            {{ __('Persatuan Aktif') }}: {{ $activeAssociation?->name ?: __('Tiada') }}
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2.5 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">{{ __('Nama Yuran') }}</th>
                        <th class="px-4 py-2.5 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">{{ __('Kekerapan') }}</th>
                        <th class="px-4 py-2.5 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">{{ __('Amaun') }}</th>
                        <th class="px-4 py-2.5 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">{{ __('Status') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse ($fees as $fee)
                        <tr>
                            <td class="px-4 py-2.5 text-gray-900">{{ $fee->name }}</td>
                            <td class="px-4 py-2.5 text-gray-700">{{ __($fee->frequencyLabel()) }}</td>
                            <td class="px-4 py-2.5 text-gray-700">RM {{ number_format((float) $fee->amount, 2) }}</td>
                            <td class="px-4 py-2.5 text-gray-700">{{ $fee->is_active ? __('Aktif') : __('Tidak Aktif') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-gray-500">{{ __('Tiada yuran aktif ditemui.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
