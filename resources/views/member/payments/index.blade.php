<x-app-layout>
    <x-slot name="header">
        <h2 class="text-lg font-semibold leading-tight text-gray-800">
            {{ __('Bayaran Saya') }}
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
                        <th class="px-4 py-2.5 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">{{ __('Tarikh') }}</th>
                        <th class="px-4 py-2.5 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">{{ __('Yuran') }}</th>
                        <th class="px-4 py-2.5 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">{{ __('Amaun') }}</th>
                        <th class="px-4 py-2.5 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">{{ __('Status') }}</th>
                        <th class="px-4 py-2.5 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">{{ __('Rujukan') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse ($payments as $payment)
                        <tr>
                            <td class="px-4 py-2.5 text-gray-700">{{ optional($payment->paid_at)->format('d/m/Y') ?: '—' }}</td>
                            <td class="px-4 py-2.5 text-gray-900">{{ $payment->fee?->name ?: '—' }}</td>
                            <td class="px-4 py-2.5 text-gray-700">RM {{ number_format((float) $payment->amount, 2) }}</td>
                            <td class="px-4 py-2.5 text-gray-700">{{ ucfirst($payment->status) }}</td>
                            <td class="px-4 py-2.5 text-gray-700">{{ $payment->reference ?: '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-gray-500">{{ __('Tiada rekod bayaran.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($payments->hasPages())
            <div class="border-t border-gray-200 bg-gray-50 px-4 py-3">
                {{ $payments->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
