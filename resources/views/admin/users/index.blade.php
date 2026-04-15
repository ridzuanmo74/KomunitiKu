<x-app-layout>
    <x-slot name="header">
        <h2 class="text-lg font-semibold leading-tight text-gray-800">
            {{ __('Users') }}
        </h2>
    </x-slot>

    <div class="overflow-hidden rounded-lg border border-gray-200 bg-white">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-4 py-2.5 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">
                            {{ __('Name') }}
                        </th>
                        <th scope="col" class="px-4 py-2.5 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">
                            {{ __('Email') }}
                        </th>
                        <th scope="col" class="px-4 py-2.5 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">
                            {{ __('Roles') }}
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse ($users as $user)
                        <tr class="hover:bg-gray-50/80">
                            <td class="whitespace-nowrap px-4 py-2.5 font-medium text-gray-900">
                                {{ $user->name }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-2.5 text-gray-700">
                                {{ $user->email }}
                            </td>
                            <td class="px-4 py-2.5 text-gray-700">
                                {{ $user->roles->pluck('name')->join(', ') ?: '—' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-4 py-8 text-center text-gray-500">
                                {{ __('No users found.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($users->hasPages())
            <div class="border-t border-gray-200 bg-gray-50 px-4 py-3">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
