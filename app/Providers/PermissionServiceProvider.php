<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use App\Models\Permission;

class PermissionServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        try {
            Permission::get()->map(function ($permission) {
                Gate::define($permission->slug, function ($user) use ($permission) {
                    return $user->hasPermission($permission->slug);
                });
            });
        } catch (\Exception $e) {
            report($e);
            return;
        }

        // Blade directives
        Blade::directive('role', function ($role) {
            return "<?php if(auth()->check() && auth()->user()->hasRole({$role})): ?>";
        });

        Blade::directive('endrole', function () {
            return "<?php endif; ?>";
        });

        Blade::directive('permission', function ($permission) {
            return "<?php if(auth()->check() && auth()->user()->hasPermission({$permission})): ?>";
        });

        Blade::directive('endpermission', function () {
            return "<?php endif; ?>";
        });
    }
}
