<x-app-layout>
    <x-slot name="header">
        <h2 class="text-lg font-semibold leading-tight text-gray-800">
            {{ __('Daftar persatuan baharu') }}
        </h2>
    </x-slot>

    <div class="rounded-lg border border-gray-200 bg-white p-4 sm:p-6">
        <form method="post" action="{{ route('committee.associations.store') }}" class="space-y-6">
            @csrf
            @include('committee.associations.partials.form-fields', ['association' => null, 'states' => $states])
            <div class="flex flex-wrap gap-2">
                <button type="submit" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
                    {{ __('Simpan') }}
                </button>
                <a href="{{ route('committee.associations.info') }}" class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    {{ __('Batal') }}
                </a>
            </div>
        </form>
    </div>
</x-app-layout>
