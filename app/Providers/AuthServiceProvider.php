<?php

namespace App\Providers;

use App\Models\Menu;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Policies\MenuPolicy;
use App\Policies\PermissionPolicy;
use App\Policies\RolePolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        User::class => UserPolicy::class,
        Role::class => RolePolicy::class,
        Permission::class => PermissionPolicy::class,
        Menu::class => MenuPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        // Super-Admin bypasses all permission checks (solo aplica a staff, guard "web")
        Gate::before(function ($user, string $_ability) {
            if ($user instanceof User && $user->hasRole('Super-Admin')) {
                return true;
            }
        });
    }
}
