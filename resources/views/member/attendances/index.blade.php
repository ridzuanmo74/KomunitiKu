<x-app-layout>
    <x-slot name="header">
        <h2 class="text-lg font-semibold leading-tight text-gray-800">
            {{ __('Kehadiran') }}
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
                        <th class="px-4 py-2.5 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">{{ __('Aktiviti') }}</th>
                        <th class="px-4 py-2.5 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">{{ __('Status') }}</th>
                        <th class="px-4 py-2.5 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">{{ __('Daftar Masuk') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse ($attendances as $attendance)
                        <tr>
                            <td class="px-4 py-2.5 text-gray-900">{{ $attendance->activity?->title ?: '—' }}</td>
                            <td class="px-4 py-2.5 text-gray-700">{{ ucfirst($attendance->status) }}</td>
                            <td class="px-4 py-2.5 text-gray-700">{{ optional($attendance->checked_in_at)->format('d/m/Y H:i') ?: '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-4 py-8 text-center text-gray-500">{{ __('Tiada rekod kehadiran ditemui.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($attendances->hasPages())
            <div class="border-t border-gray-200 bg-gray-50 px-4 py-3">
                {{ $attendances->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
