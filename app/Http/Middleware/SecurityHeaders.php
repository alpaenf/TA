<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Prevent clickjacking attacks
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        // Prevent MIME type sniffing
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // Enable XSS protection (legacy browsers)
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // Referrer policy - only send origin on cross-origin requests
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Allow Vite dev server in local development
        $viteDevServer = app()->environment('local', 'testing')
            ? ' http://localhost:5173 http://[::1]:5173 ws://localhost:5173 ws://[::1]:5173'
            : '';

        // Content Security Policy - allow same origin and inline scripts (needed for Vue/Inertia)
        $csp = [
            "default-src 'self'" . $viteDevServer,
            "script-src 'self' 'unsafe-inline' 'unsafe-eval'" . $viteDevServer,
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com" . $viteDevServer,
            "img-src 'self' data: blob: https:" . $viteDevServer,
            "font-src 'self' data: https://fonts.gstatic.com" . $viteDevServer,
            "connect-src 'self' https://www.google.com https://generativelanguage.googleapis.com" . $viteDevServer,
            "frame-src 'self' https://www.google.com",
            "frame-ancestors 'self'",
        ];
        $response->headers->set('Content-Security-Policy', implode('; ', $csp));

        // Force HTTPS in production
        if (app()->environment('production')) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        // Permissions Policy (formerly Feature Policy)
        // Allow camera for QR Scanner feature, microphone disabled, geolocation for self
        $response->headers->set('Permissions-Policy', 'camera=(self), microphone=(), geolocation=(self)');

        return $response;
    }
}
