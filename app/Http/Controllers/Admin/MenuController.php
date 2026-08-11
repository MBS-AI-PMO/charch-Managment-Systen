<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\MenuRequest;
use App\Models\Menu;
use App\Models\Page;
use Illuminate\Support\Facades\Route as RouteFacade;

class MenuController extends Controller
{
    public function index()
    {
        $menus = Menu::withCount('items')->orderBy('name')->get();
        return view('admin.menus.index', compact('menus'));
    }
    
    public function create()
    {
        return view('admin.menus.create', ['menu' => new Menu()]);
    }

    public function store(MenuRequest $req)
    {
        $menu = Menu::create($req->validated());
        return redirect()->route('admin.menus.edit', $menu)->with('success', 'Menu created.');
    }

    public function edit(Menu $menu)
    {
        $menu->load(['items' => fn ($q) => $q->orderBy('sort_order')]);
        $pages = Page::orderBy('title')->get(['id', 'slug', 'title']);
        $routes = $this->siteRoutes();
        $rootItems = $menu->items->whereNull('parent_id')->values();
        return view('admin.menus.edit', compact('menu', 'pages', 'routes', 'rootItems'));
    }

    public function update(MenuRequest $req, Menu $menu)
    {
        $menu->update($req->validated());
        return redirect()->route('admin.menus.edit', $menu)->with('success', 'Menu updated.');
    }

    public function show(Menu $menu)
    {
        return redirect()->route('admin.menus.edit', $menu);
    }

    public function destroy(Menu $menu)
    {
        $menu->delete();
        return redirect()->route('admin.menus.index')->with('success', 'Menu deleted.');
    }

    /**
     * Return all named `site.*` routes for the link picker.
     *
     * @return array<int,string>
     */
    protected function siteRoutes(): array
    {
        $names = [];
        foreach (RouteFacade::getRoutes() as $r) {
            $name = $r->getName();
            if ($name && str_starts_with($name, 'site.')) {
                $names[] = $name;
            }
        }
        sort($names);
        return array_values(array_unique($names));
    }
}
