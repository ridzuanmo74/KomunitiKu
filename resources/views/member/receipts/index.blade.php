<x-app-layout>
    <x-slot name="header">
        <h2 class="text-lg font-semibold leading-tight text-gray-800">
            {{ __('Resit') }}
        </h2>
    </x-slot>

    <div class="overflow-hidden rounded-lg border border-gray-200 bg-white">
        <div class="border-b border-gray-200 px-4 py-3 text-sm text-gray-600">
            {{ __('Resit bayaran berjaya bagi persatuan aktif.') }}
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2.5 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">{{ __('No Rujukan') }}</th>
                        <th class="px-4 py-2.5 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">{{ __('Yuran') }}</th>
                        <th class="px-4 py-2.5 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">{{ __('Tarikh Bayar') }}</th>
                        <th class="px-4 py-2.5 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">{{ __('Amaun') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse ($receipts as $receipt)
                        <tr>
                            <td class="px-4 py-2.5 text-gray-700">{{ $receipt->reference ?: '—' }}</td>
                            <td class="px-4 py-2.5 text-gray-900">{{ $receipt->fee?->name ?: '—' }}</td>
                            <td class="px-4 py-2.5 text-gray-700">{{ optional($receipt->paid_at)->format('d/m/Y H:i') ?: '—' }}</td>
                            <td class="px-4 py-2.5 text-gray-700">RM {{ number_format((float) $receipt->amount, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-gray-500">{{ __('Tiada resit ditemui.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($receipts->hasPages())
            <div class="border-t border-gray-200 bg-gray-50 px-4 py-3">
                {{ $receipts->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
