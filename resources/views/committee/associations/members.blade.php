<x-app-layout>
    <x-slot name="header">
        <h2 class="text-lg font-semibold leading-tight text-gray-800">
            {{ __('Ahli Persatuan') }}
        </h2>
    </x-slot>

    <div class="overflow-hidden rounded-lg border border-gray-200 bg-white">
        <div class="border-b border-gray-200 px-4 py-3 text-sm text-gray-600">
            {{ __('Persatuan') }}: {{ $association?->name ?: __('Tiada') }}
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2.5 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">{{ __('Nama') }}</th>
                        <th class="px-4 py-2.5 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">{{ __('Emel') }}</th>
                        <th class="px-4 py-2.5 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">{{ __('No Ahli') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse ($members as $member)
                        <tr>
                            <td class="px-4 py-2.5 text-gray-900">{{ $member->name }}</td>
                            <td class="px-4 py-2.5 text-gray-700">{{ $member->email }}</td>
                            <td class="px-4 py-2.5 text-gray-700">{{ $member->pivot?->membership_no ?: '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-4 py-8 text-center text-gray-500">{{ __('Tiada ahli untuk dipaparkan.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
