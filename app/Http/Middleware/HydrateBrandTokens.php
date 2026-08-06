<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HydrateBrandTokens
{
    /**
     * Share brand-color CSS variables (as "R G B" triples for Tailwind's
     * rgb(var(--brand-primary)) tokens) with every Blade view.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $hex2rgb = function (?string $hex, string $fallback): array {
            $hex = $hex ? ltrim($hex, '#') : '';
            if (strlen($hex) !== 6 || ! ctype_xdigit($hex)) {
                $hex = ltrim($fallback, '#');
            }
            $rgb = sscanf($hex, '%02x%02x%02x');

            return is_array($rgb) ? $rgb : [0, 0, 0];
        };

        $primary = $hex2rgb(settings('brand.color.primary', '#7A1F2B'), '#7A1F2B');
        $secondary = $hex2rgb(settings('brand.color.secondary', '#C9A961'), '#C9A961');

        view()->share('brandPrimaryRgb', implode(' ', $primary));
        view()->share('brandSecondaryRgb', implode(' ', $secondary));
        view()->share('brandName', settings('brand.name', 'Assemblies of God'));
        view()->share('brandTagline', settings('brand.tagline', 'Rawalpindi'));
        view()->share('brandLogoUrl', ($logo = settings('brand.logo')) ? site_storage_url($logo) : null);
        view()->share('brandFaviconUrl', ($favicon = settings('brand.favicon')) ? site_storage_url($favicon) : null);

        return $next($request);
    }
}
