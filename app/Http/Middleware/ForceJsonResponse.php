<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForceJsonResponse
{
    /**
     * Handle an incoming request and force JSON response.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Force request to expect JSON
        $request->headers->set('Accept', 'application/json');
        
        $response = $next($request);
        
        // FORCE Content-Type to application/json for all responses
        $contentType = $response->headers->get('Content-Type');
        
        // If response has content and is not already JSON
        if (!str_contains($contentType ?? '', 'application/json')) {
            $content = $response->getContent();
            
            // Check if content is valid JSON
            json_decode($content);
            if (json_last_error() === JSON_ERROR_NONE) {
                // Content is JSON, just fix the header
                $response->headers->set('Content-Type', 'application/json');
            } else if ($response->getStatusCode() >= 400) {
                // Error response but not JSON, convert it
                $errorMessage = 'Terjadi kesalahan server';
                if (preg_match('/<title>(.*?)<\/title>/i', $content, $matches)) {
                    $errorMessage = strip_tags($matches[1]);
                }
                
                return response()->json([
                    'error' => $errorMessage
                ], $response->getStatusCode());
            }
        }
        
        return $response;
    }
}
