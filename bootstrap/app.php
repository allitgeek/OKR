<?php

// Force disable error reporting
error_reporting(0);
ini_set('display_errors', '0');
ini_set('display_startup_errors', '0');

// Start output buffering
ob_start(function($buffer) {
    // Remove any content before <!DOCTYPE html> or <html
    if (preg_match('/(<!DOCTYPE html|<html)/i', $buffer, $matches, PREG_OFFSET_CAPTURE)) {
        return substr($buffer, $matches[0][1]);
    }
    return $buffer;
});

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->reportable(function (\Throwable $e) {
            error_reporting(0);
            ini_set('display_errors', '0');
        });
    })->create();
