<?php

namespace App\Providers;

use App\Services\SettingsRepository;
use Illuminate\Support\ServiceProvider;

class SettingsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(SettingsRepository::class);
    }

    public function boot(): void
    {
        //
    }
}
