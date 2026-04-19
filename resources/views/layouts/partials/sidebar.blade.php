<div
    x-data="{ open: @js($sidebarOpenGroups) }"
    class="space-y-0.5 text-sm"
>
    @foreach ($sidebarItems as $item)
        @if (($item['type'] ?? '') === 'link')
            <a
                href="{{ route($item['route']) }}"
                @class([
                    'block rounded-md px-2.5 py-1.5 font-medium transition-colors',
                    'border-l-2 border-kk-accent bg-kk-sidebar-active text-kk-sidebar-text' => request()->routeIs($item['route_is'] ?? ''),
                    'border-l-2 border-transparent text-kk-sidebar-text hover:bg-kk-sidebar-hover' => ! request()->routeIs($item['route_is'] ?? ''),
                ])
            >
                {{ __($item['label']) }}
            </a>
        @elseif (($item['type'] ?? '') === 'group' && ! empty($item['children']) && ! empty($item['id']))
            @php
                $groupId = $item['id'];
                $groupActive = collect($item['children'])->contains(fn ($c) => request()->routeIs($c['route_is'] ?? ''));
            @endphp
            <div class="pt-2 first:pt-0">
                <button
                    type="button"
                    id="sidebar-group-trigger-{{ $groupId }}"
                    class="flex w-full items-center justify-between rounded-md px-2.5 py-1.5 text-left text-xs font-semibold uppercase tracking-wider text-kk-sidebar-muted hover:bg-kk-sidebar-hover hover:text-kk-sidebar-text focus:outline-none focus:ring-2 focus:ring-kk-accent/40 focus:ring-offset-1 focus:ring-offset-kk-sidebar"
                    :aria-expanded="open['{{ $groupId }}']"
                    aria-controls="sidebar-group-{{ $groupId }}"
                    @click="open['{{ $groupId }}'] = ! open['{{ $groupId }}']"
                >
                    <span class="{{ $groupActive ? 'text-kk-sidebar-text' : '' }}">{{ __($item['label']) }}</span>
                    <svg
                        class="h-4 w-4 shrink-0 text-kk-sidebar-muted transition-transform duration-150"
                        :class="{ 'rotate-180': open['{{ $groupId }}'] }"
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="1.5"
                        stroke="currentColor"
                        aria-hidden="true"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                    </svg>
                </button>
                <ul
                    x-cloak
                    x-show="open['{{ $groupId }}']"
                    id="sidebar-group-{{ $groupId }}"
                    role="list"
                    class="ml-2 mt-1 space-y-0.5 border-l border-kk-sidebar-border py-0.5 pl-3"
                >
                    @foreach ($item['children'] as $child)
                        @if (($child['type'] ?? '') === 'link')
                            <li>
                                <a
                                    href="{{ route($child['route']) }}"
                                    @class([
                                        'block rounded-md py-1.5 pl-1 pr-2 text-sm font-medium transition-colors',
                                        'bg-kk-sidebar-active text-kk-sidebar-text ring-1 ring-kk-border/60' => request()->routeIs($child['route_is'] ?? ''),
                                        'text-kk-sidebar-text hover:bg-kk-sidebar-hover' => ! request()->routeIs($child['route_is'] ?? ''),
                                    ])
                                >
                                    {{ __($child['label']) }}
                                </a>
                            </li>
                        @endif
                    @endforeach
                </ul>
            </div>
        @endif
    @endforeach
</div>
