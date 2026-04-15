<?php

namespace App\Providers;

use App\Policies\RolePolicy;
use App\Support\NavigationMenu;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Spatie\Permission\Models\Role;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Role::class, RolePolicy::class);

        View::composer('layouts.partials.sidebar', function ($view): void {
            $user = auth()->user();
            $items = NavigationMenu::sidebarItems($user);
            $view->with([
                'sidebarItems' => $items,
                'sidebarOpenGroups' => NavigationMenu::sidebarOpenGroups($items),
            ]);
        });
    }
}
