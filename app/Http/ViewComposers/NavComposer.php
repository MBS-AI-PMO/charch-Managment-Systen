<?php

namespace App\Http\ViewComposers;

use App\Models\Menu;
use App\Models\MenuItem;
use Illuminate\Support\Facades\Route;
use Illuminate\View\View;

class NavComposer
{
    protected ?array $cachedNav = null;

    public function compose(View $view): void
    {
        $view->with('siteNav', $this->buildNav());
    }

    /**
     * Convenience accessor used by view()->share() bootstrapping so we can
     * expose the nav as a globally-shared variable rather than relying on
     * per-view composition (which doesn't reach @extends child scopes).
     */
    public function nav(): array
    {
        return $this->buildNav();
    }

    protected function buildNav(): array
    {
        if ($this->cachedNav !== null) {
            return $this->cachedNav;
        }

        $main = Menu::with([
            'topLevelItems' => fn ($q) => $q->with('children'),
        ])->where('slug', 'main')->first();

        if (! $main) {
            return $this->cachedNav = [];
        }

        $this->cachedNav = $main->topLevelItems->map(fn (MenuItem $i) => [
            'label' => $i->label,
            'url' => $this->resolveUrl($i),
            'target' => $i->target ?: '_self',
            'children' => $i->children->map(fn (MenuItem $c) => [
                'label' => $c->label,
                'url' => $this->resolveUrl($c),
                'target' => $c->target ?: '_self',
            ])->all(),
        ])->all();

        return $this->cachedNav;
    }

    protected function resolveUrl(MenuItem $item): string
    {
        $value = (string) $item->link_value;

        // Guard: route names sometimes saved as link_type=url (e.g. "site.home").
        if ($item->link_type === 'url' && str_contains($value, '.') && Route::has($value)) {
            return route($value, [], false);
        }

        return match ($item->link_type) {
            'route' => Route::has($value) ? route($value, [], false) : '#',
            'url' => str_starts_with($value, 'http://') || str_starts_with($value, 'https://') || str_starts_with($value, '/')
                ? $value
                : '/'.ltrim($value, '/'),
            'page' => Route::has('site.page')
                ? route('site.page', ['slug' => $value], false)
                : '/'.ltrim($value, '/'),
            default => '#',
        };
    }
}
