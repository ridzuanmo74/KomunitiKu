<x-app-layout>
    <x-slot name="header">
        <h2 class="text-lg font-semibold leading-tight text-gray-800">
            {{ __('Roles') }}
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
                            {{ __('Guard') }}
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse ($roles as $role)
                        <tr class="hover:bg-gray-50/80">
                            <td class="whitespace-nowrap px-4 py-2.5 font-medium text-gray-900">
                                {{ $role->name }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-2.5 text-gray-700">
                                {{ $role->guard_name }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="px-4 py-8 text-center text-gray-500">
                                {{ __('No roles found.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($roles->hasPages())
            <div class="border-t border-gray-200 bg-gray-50 px-4 py-3">
                {{ $roles->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
