<?php

if (! function_exists('settings')) {
    /**
     * Get a site setting value, or the repository when no key is given.
     */
    function settings(?string $key = null, mixed $default = null)
    {
        $repo = app(\App\Services\SettingsRepository::class);

        return $key === null ? $repo : $repo->get($key, $default);
    }
}

if (! function_exists('formatMoney')) {
    function formatMoney(int|float|null $cents, ?string $symbol = null): string
    {
        $symbol ??= settings('finance.currency_symbol', '$');
        $cents = (int) ($cents ?? 0);
        return $symbol.number_format($cents / 100, 2);
    }
}

if (! function_exists('weekday')) {
    function weekday(string $name): int
    {
        return match (strtolower($name)) {
            'sunday' => 0, 'monday' => 1, 'tuesday' => 2, 'wednesday' => 3,
            'thursday' => 4, 'friday' => 5, 'saturday' => 6,
            default => 6,
        };
    }
}

if (! function_exists('site_storage_url')) {
    function site_storage_url(?string $path): ?string
    {
        if (! $path) {
            return null;
        }
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            $parsed = parse_url($path, PHP_URL_PATH);
            if (is_string($parsed) && preg_match('#/storage/(.+)$#', $parsed, $m)) {
                return asset('storage/'.$m[1]);
            }

            return $path;
        }
        $path = ltrim($path, '/');
        if (str_starts_with($path, 'storage/')) {
            $path = substr($path, 8);
        }

        return asset('storage/'.$path);
    }
}

if (! function_exists('site_img')) {
    function site_img(?string $path, string $seed, int $w, int $h): string
    {
        if ($path) {
            $clean = ltrim($path, '/');
            if (str_starts_with($clean, 'storage/')) {
                $clean = substr($clean, 8);
            }
            if (str_starts_with($clean, 'http://') || str_starts_with($clean, 'https://')) {
                return site_storage_url($path) ?? "https://picsum.photos/seed/{$seed}/{$w}/{$h}";
            }
            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($clean)) {
                return site_storage_url($clean);
            }
        }

        return "https://picsum.photos/seed/{$seed}/{$w}/{$h}";
    }
}

if (! function_exists('site_render_html')) {
    function site_render_html(?string $html): string
    {
        if (! $html) {
            return '';
        }

        return preg_replace_callback(
            '/(<img\b[^>]*\bsrc=["\'])([^"\']+)(["\'])/i',
            function (array $m): string {
                return $m[1].(site_storage_url($m[2]) ?? $m[2]).$m[3];
            },
            $html
        );
    }
}
