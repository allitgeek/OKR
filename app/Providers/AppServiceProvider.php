<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

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
    }
}
