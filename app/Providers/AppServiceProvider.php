<?php

namespace App\Providers;

use App\Models\Association;
use App\Policies\AssociationPolicy;
use App\Policies\RolePolicy;
use App\Services\MemberPortalService;
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
        Gate::policy(Association::class, AssociationPolicy::class);

        View::composer('layouts.partials.sidebar', function ($view): void {
            $user = auth()->user();
            $items = NavigationMenu::sidebarItems($user);
            $view->with([
                'sidebarItems' => $items,
                'sidebarOpenGroups' => NavigationMenu::sidebarOpenGroups($items),
            ]);
        });

        View::composer('layouts.app', function ($view): void {
            $user = auth()->user();
            $activeAssociationName = null;

            if ($user && ! $user->isSuperAdmin()) {
                $activeAssociation = app(MemberPortalService::class)->activeAssociationFor($user);
                $activeAssociationName = $activeAssociation?->name;
            }

            $view->with('activeAssociationName', $activeAssociationName);
        });
    }
}
