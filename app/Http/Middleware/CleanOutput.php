<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CleanOutput
{
    public function handle(Request $request, Closure $next)
    {
        // Start output buffering
        ob_start();
        
        // Get the response
        $response = $next($request);
        
        // Get the content
        $content = ob_get_clean();
        
        // Remove any content before <!DOCTYPE html> or <html
        if (preg_match('/(<!DOCTYPE html|<html)/i', $content, $matches, PREG_OFFSET_CAPTURE)) {
            $content = substr($content, $matches[0][1]);
        }
        
        // If it's an HTML response, clean it
        if ($response->headers->get('content-type') === 'text/html') {
            $response->setContent($content);
        }
        
        return $response;
    }
} 