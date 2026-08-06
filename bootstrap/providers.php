<?php

use App\Providers\AppServiceProvider;
use App\Providers\RateLimitServiceProvider;
use App\Providers\SettingsServiceProvider;
use App\Providers\ViewComposerServiceProvider;

return [
    AppServiceProvider::class,
    RateLimitServiceProvider::class,
    SettingsServiceProvider::class,
    ViewComposerServiceProvider::class,
];
