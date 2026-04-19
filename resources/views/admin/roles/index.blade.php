<x-app-layout>
    <x-slot name="header">
        <h2 class="text-lg font-semibold leading-tight">
            {{ __('Roles') }}
        </h2>
    </x-slot>

    <div class="space-y-4">
        <div class="kk-card overflow-hidden p-0">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-kk-border/60 text-sm">
                    <thead class="bg-kk-sidebar-hover/80">
                        <tr>
                            <th scope="col" class="px-4 py-2.5 text-left text-xs font-semibold uppercase tracking-wide text-kk-sidebar-muted">
                                {{ __('Name') }}
                            </th>
                            <th scope="col" class="px-4 py-2.5 text-left text-xs font-semibold uppercase tracking-wide text-kk-sidebar-muted">
                                {{ __('Guard') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-kk-border/40 bg-kk-surface">
                        @forelse ($roles as $role)
                            <tr class="hover:bg-kk-sidebar-hover/50">
                                <td class="whitespace-nowrap px-4 py-2.5 font-medium text-gray-900">
                                    {{ $role->name }}
                                </td>
                                <td class="whitespace-nowrap px-4 py-2.5 text-gray-700">
                                    {{ $role->guard_name }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="px-4 py-10 text-center text-gray-500">
                                    {{ __('No roles found.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($roles->hasPages())
                <div class="border-t border-kk-border bg-kk-surface-muted px-4 py-3">
                    {{ $roles->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
