<?php

namespace App\Providers;

use App\Http\ViewComposers\NavComposer;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewComposerServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Compose siteNav onto every Blade view rendered. The composer fires
        // lazily per render, so it's safe even when the DB is unavailable at
        // boot (artisan, console commands, etc.) — and using `*` ensures it
        // reaches @section blocks of child templates, not just the layout.
        View::composer('*', NavComposer::class);
    }
}
