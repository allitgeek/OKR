<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        error_reporting(0);
        ini_set('display_errors', '0');
        ini_set('display_startup_errors', '0');
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Disable error display
        error_reporting(0);
        ini_set('display_errors', '0');
        ini_set('display_startup_errors', '0');
        
        // Set custom error handler
        set_error_handler(function($errno, $errstr, $errfile, $errline) {
            return true; // Suppress all errors
        });

        // Performance Optimizations
        if (!app()->isLocal()) {
            // Disable query log
            DB::connection()->disableQueryLog();
            
            // Enable model lazy loading strict mode to prevent N+1 issues
            \Illuminate\Database\Eloquent\Model::preventLazyLoading();
            
            // Increase PHP memory limit
            ini_set('memory_limit', '256M');
            
            // Enable OPcache
            if (function_exists('opcache_enable')) {
                opcache_enable();
            }
        }
    }
}
