<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $res = $next($request);

        $res->headers->set('X-Content-Type-Options', 'nosniff');
        $res->headers->set('X-Frame-Options', 'DENY');
        $res->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $res->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');

        if (app()->isProduction()) {
            $res->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        // CSP allowing self + Google Fonts + YouTube/Vimeo embeds + Google Maps + reCAPTCHA.
        $csp = "default-src 'self'; "
            . "img-src 'self' data: https:; "
            . "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; "
            . "font-src 'self' https://fonts.gstatic.com; "
            . "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://www.google.com https://www.gstatic.com; "
            . "frame-src 'self' https://www.youtube.com https://www.youtube-nocookie.com https://player.vimeo.com https://www.google.com; "
            . "connect-src 'self'; "
            . "form-action 'self'; "
            . "frame-ancestors 'none'; "
            . "base-uri 'self';";
        $res->headers->set('Content-Security-Policy', $csp);

        return $res;
    }
}
