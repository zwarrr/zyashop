<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        //
    ];

    /**
     * Determine if the session and input CSRF tokens match.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function tokensMatch($request)
    {
        // Get token from request
        $token = $this->getTokenFromRequest($request);

        // If no token in request, check if it's in the session
        if (!$token && $request->session()) {
            return true; // Allow request to proceed if session exists
        }

        return parent::tokensMatch($request);
    }

    /**
     * Add the CSRF token to the response cookies.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Symfony\Component\HttpFoundation\Response  $response
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function addCookieToResponse($request, $response)
    {
        $config = config('session');

        if ($response instanceof \Illuminate\Http\Response) {
            $response->headers->setCookie(
                $this->newCookie($request, $config)
            );
        }

        return $response;
    }

    /**
     * Create a new cookie instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  array  $config
     * @return \Symfony\Component\HttpFoundation\Cookie
     */
    protected function newCookie($request, $config)
    {
        return cookie(
            'XSRF-TOKEN',
            $request->session()->token(),
            $config['lifetime'],
            $config['path'],
            $config['domain'] ?? null,
            $config['secure'] ?? true,
            false,
            false,
            $config['same_site'] ?? 'lax'
        );
    }
}
