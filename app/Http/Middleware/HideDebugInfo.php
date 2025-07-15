<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class HideDebugInfo
{
    public function handle(Request $request, Closure $next)
    {
        // Disable error reporting for this request
        error_reporting(0);
        ini_set('display_errors', '0');
        
        return $next($request);
    }
} 