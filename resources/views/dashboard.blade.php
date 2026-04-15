<x-app-layout>
    <x-slot name="header">
        <h2 class="text-lg font-semibold leading-tight text-gray-800">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div>
        <div class="rounded-lg border border-gray-200 bg-white">
            <div class="p-4 text-gray-900 sm:p-6">
                {{ __("You're logged in!") }}
            </div>
        </div>
    </div>
</x-app-layout>
