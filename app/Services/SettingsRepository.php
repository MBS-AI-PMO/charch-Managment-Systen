<?php

namespace App\Services;

use App\Models\SiteSetting;
use Illuminate\Support\Facades\Cache;

class SettingsRepository
{
    protected const KEY = 'site_settings';

    public function all(): array
    {
        return Cache::rememberForever(self::KEY, fn () =>
            SiteSetting::pluck('value', 'key')->toArray()
        );
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->all()[$key] ?? $default;
    }

    public function set(string $key, mixed $value): void
    {
        SiteSetting::updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget(self::KEY);
    }

    public function flush(): void
    {
        Cache::forget(self::KEY);
    }
}
