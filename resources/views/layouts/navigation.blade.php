<nav x-data="{ open: false, notifOpen: false }" class="h-14 shrink-0 border-b border-kk-nav-from bg-gradient-to-r from-kk-nav-from to-kk-nav-to shadow-md">
    <!-- Primary Navigation Menu -->
    <div class="mx-auto h-full w-full px-4 sm:px-6 lg:px-8">
        <div class="flex h-14 justify-between">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center text-kk-nav-fg">
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 text-kk-nav-fg hover:text-white">
                        <x-application-logo class="h-6 w-6 shrink-0 fill-current text-kk-nav-fg" />
                        <span class="text-sm font-semibold tracking-wide text-kk-nav-fg">
                            {{ config('app.name', 'KomunitiKu') }}
                        </span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="flex items-center space-x-4 ms-4 sm:ms-8">
                    <a href="{{ route('dashboard') }}"
                       class="inline-flex h-14 items-center border-b-2 px-1 text-sm font-medium {{ request()->routeIs('dashboard') ? 'border-kk-nav-fg text-white' : 'border-transparent text-kk-nav-muted hover:text-white' }}">
                        {{ __('Dashboard') }}
                    </a>
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <div class="relative me-3" @click.away="notifOpen = false">
                    <button type="button"
                            @click="notifOpen = !notifOpen"
                            class="inline-flex h-10 w-10 items-center justify-center rounded-full text-kk-nav-muted transition hover:bg-white/10 hover:text-kk-nav-fg focus:outline-none focus:ring-2 focus:ring-kk-nav-fg/60 focus:ring-offset-2 focus:ring-offset-kk-nav-to"
                            aria-label="{{ __('Notifications') }}">
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.85 23.85 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75v-.7V9a6 6 0 1 0-12 0v.05c0 .238 0 .476.001.7a8.967 8.967 0 0 1-2.312 6.022 23.85 23.85 0 0 0 5.454 1.31m5.714 0a3 3 0 1 1-5.714 0m5.714 0H9.143" />
                        </svg>
                    </button>

                    <div x-cloak
                         x-show="notifOpen"
                         x-transition
                         class="absolute right-0 z-50 mt-2 w-72 overflow-hidden rounded-lg border border-kk-border bg-kk-surface shadow-xl ring-1 ring-kk-border/40">
                        <div class="border-b border-kk-border bg-gradient-to-r from-kk-modal-from to-kk-modal-to px-4 py-3">
                            <p class="text-sm font-semibold text-kk-nav-fg">{{ __('Notifications') }}</p>
                        </div>
                        <div class="px-4 py-6 text-sm text-kk-sidebar-muted">
                            {{ __('No new notifications right now.') }}
                        </div>
                    </div>
                </div>

                <x-dropdown align="right" width="48" contentClasses="bg-kk-surface py-1 shadow-lg">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center rounded-md border border-transparent bg-transparent px-3 py-2 text-sm font-medium leading-4 text-kk-nav-muted hover:text-kk-nav-fg focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center rounded-md p-2 text-kk-nav-muted hover:bg-white/10 hover:text-kk-nav-fg focus:outline-none transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden border-t border-white/10 bg-kk-nav-to sm:hidden">
        <div class="space-y-1 pt-2 pb-3">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="border-t border-white/10 pt-4 pb-1">
            <div class="px-4">
                <div class="text-base font-medium text-kk-nav-fg">{{ Auth::user()->name }}</div>
                <div class="text-sm font-medium text-kk-nav-muted">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
