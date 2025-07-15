<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        // Add this to prevent debug information from being displayed
        $this->renderable(function (Throwable $e) {
            if (!config('app.debug')) {
                return response()->view('errors.500', [], 500);
            }
        });
    }

    /**
     * Determine if the exception should be reported.
     */
    public function shouldReport(Throwable $e): bool
    {
        error_reporting(0);
        ini_set('display_errors', '0');
        return parent::shouldReport($e);
    }
} 