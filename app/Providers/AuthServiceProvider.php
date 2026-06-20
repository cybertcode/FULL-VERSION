<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Policies del proyecto.
     * Formato: Model::class => Policy::class
     */
    protected array $policies = [
        // 'App\Models\User' => 'App\Policies\UserPolicy',
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        // Super-Admin bypasses all permission checks
        Gate::before(function ($user, $ability) {
            if ($user->hasRole('Super-Admin')) {
                return true;
            }
        });
    }

    protected function registerPolicies(): void
    {
        foreach ($this->policies as $model => $policy) {
            Gate::policy($model, $policy);
        }
    }
}
