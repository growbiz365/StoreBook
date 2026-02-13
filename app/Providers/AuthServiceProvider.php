<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Business;
use Illuminate\Support\Facades\Log;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        \App\Models\OtherIncome::class => \App\Policies\OtherIncomePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        Gate::define('module', function ($user, $moduleName) {
            $activeBusinessId = session('active_business');
            \Log::info('Gate:module called', [
                'user_id' => $user->id,
                'module' => $moduleName,
                'active_business' => $activeBusinessId
            ]);

            if (!$activeBusinessId) {
                $result = $user->can($moduleName);
                \Log::info('No active business, fallback to user permission', [
                    'result' => $result
                ]);
                return $result;
            }

            $business = \App\Models\Business::with('package.modules')->find($activeBusinessId);
            if (!$business || !$user->businesses()->where('business_id', $activeBusinessId)->exists()) {
                \Log::warning('Business not found or user not attached', [
                    'business_found' => !!$business,
                    'user_attached' => $user->businesses()->where('business_id', $activeBusinessId)->exists()
                ]);
                return false;
            }

            if (!$business->package) {
                \Log::warning('Business has no package', [
                    'business_id' => $activeBusinessId
                ]);
                return false;
            }

            $modules = $business->package->modules;
            $moduleAvailable = $modules->where('name', $moduleName)->first();

            if (!$moduleAvailable) {
                \Log::warning('Module not available in business package', [
                    'module' => $moduleName
                ]);
                return false;
            }

            $result = $user->can($moduleName);
            \Log::info('Final user permission check', [
                'result' => $result
            ]);
            return $result;
        });
    }
}
