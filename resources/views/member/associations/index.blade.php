<x-app-layout>
    <x-slot name="header">
        <h2 class="text-lg font-semibold leading-tight text-gray-800">
            {{ __('Persatuan Saya') }}
        </h2>
    </x-slot>

    <div class="space-y-4">
        @if (session('status'))
            <div class="rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('status') }}
            </div>
        @endif

        <div class="overflow-hidden rounded-lg border border-gray-200 bg-white">
            <div class="border-b border-gray-200 px-4 py-3 text-sm text-gray-600">
                {{ __('Pilih persatuan aktif untuk menapis yuran, invois, dan bayaran anda.') }}
            </div>
            <div class="divide-y divide-gray-100">
                @forelse ($associations as $association)
                    <div class="flex flex-col gap-3 px-4 py-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <p class="font-medium text-gray-900">{{ $association->name }}</p>
                            <p class="text-sm text-gray-600">{{ $association->code }}</p>
                        </div>
                        <form action="{{ route('member.associations.switch') }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="association_id" value="{{ $association->id }}">
                            <button
                                type="submit"
                                class="inline-flex items-center rounded-md border px-3 py-1.5 text-sm font-medium {{ $activeAssociation?->id === $association->id ? 'border-indigo-200 bg-indigo-50 text-indigo-700' : 'border-gray-300 bg-white text-gray-700 hover:bg-gray-50' }}"
                            >
                                {{ $activeAssociation?->id === $association->id ? __('Aktif') : __('Jadikan Aktif') }}
                            </button>
                        </form>
                    </div>
                @empty
                    <div class="px-4 py-8 text-center text-sm text-gray-500">
                        {{ __('Anda belum mempunyai keahlian persatuan.') }}
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
