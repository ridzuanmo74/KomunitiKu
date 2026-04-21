<x-app-layout>
    <x-slot name="header">
        <h2 class="text-lg font-semibold leading-tight">
            {{ __('Semakan Bayaran') }}
        </h2>
    </x-slot>

    <div class="kk-card overflow-hidden p-0">
        <div class="border-b border-kk-border px-4 py-3 text-sm text-kk-sidebar-text">
            {{ __('Persatuan') }}: {{ $association?->name ?: __('Tiada') }}
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-kk-border/60 text-sm">
                <thead class="bg-kk-sidebar-hover/80">
                    <tr>
                        <th class="px-4 py-2.5 text-left text-xs font-semibold uppercase tracking-wide text-kk-sidebar-muted">{{ __('Ahli') }}</th>
                        <th class="px-4 py-2.5 text-left text-xs font-semibold uppercase tracking-wide text-kk-sidebar-muted">{{ __('Yuran') }}</th>
                        <th class="px-4 py-2.5 text-left text-xs font-semibold uppercase tracking-wide text-kk-sidebar-muted">{{ __('Amaun') }}</th>
                        <th class="px-4 py-2.5 text-left text-xs font-semibold uppercase tracking-wide text-kk-sidebar-muted">{{ __('Status') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-kk-border/40 bg-kk-surface">
                    @forelse ($payments as $payment)
                        <tr class="hover:bg-kk-sidebar-hover/50">
                            <td class="px-4 py-2.5 font-medium text-gray-900">{{ $payment->user?->name ?: '—' }}</td>
                            <td class="px-4 py-2.5 text-gray-700">{{ $payment->fee?->name ?: '—' }}</td>
                            <td class="px-4 py-2.5 text-gray-700">RM {{ number_format((float) $payment->amount, 2) }}</td>
                            <td class="px-4 py-2.5 text-gray-700">{{ ucfirst($payment->status) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-gray-500">{{ __('Tiada bayaran direkodkan.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
