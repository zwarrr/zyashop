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
        
        // If response is not JSON, convert it
        if (!$response->headers->get('Content-Type') || 
            !str_contains($response->headers->get('Content-Type'), 'application/json')) {
            
            // Check if it's an error response
            if ($response->getStatusCode() >= 400) {
                $content = $response->getContent();
                
                // Extract error message if possible
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
