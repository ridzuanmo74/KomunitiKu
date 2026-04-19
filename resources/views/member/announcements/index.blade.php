<x-app-layout>
    <x-slot name="header">
        <h2 class="text-lg font-semibold leading-tight text-gray-800">
            {{ __('Pengumuman') }}
        </h2>
    </x-slot>

    <div class="space-y-3">
        <div class="rounded-lg border border-gray-200 bg-white px-4 py-3 text-sm text-gray-600">
            {{ __('Persatuan Aktif') }}: {{ $activeAssociation?->name ?: __('Tiada') }}
        </div>

        @forelse ($announcements as $announcement)
            <article class="rounded-lg border border-gray-200 bg-white p-4">
                <h3 class="text-base font-semibold text-gray-900">{{ $announcement->title }}</h3>
                <p class="mt-1 text-xs text-gray-500">
                    {{ __('Diterbitkan') }}: {{ optional($announcement->published_at)->format('d/m/Y H:i') ?: __('Belum diterbitkan') }}
                </p>
                <p class="mt-3 text-sm text-gray-700">{{ $announcement->content }}</p>
            </article>
        @empty
            <div class="rounded-lg border border-gray-200 bg-white px-4 py-8 text-center text-sm text-gray-500">
                {{ __('Tiada pengumuman untuk persatuan ini.') }}
            </div>
        @endforelse

        @if ($announcements->hasPages())
            <div class="rounded-lg border border-gray-200 bg-gray-50 px-4 py-3">
                {{ $announcements->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
