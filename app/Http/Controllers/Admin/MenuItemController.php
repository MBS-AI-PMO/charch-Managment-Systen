<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\MenuItemRequest;
use App\Models\Menu;
use App\Models\MenuItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MenuItemController extends Controller
{
    public function index(Menu $menu)
    {
        return redirect()->route('admin.menus.edit', $menu);
    }

    public function create(Menu $menu)
    {
        return redirect()->route('admin.menus.edit', $menu);
    }

    public function store(MenuItemRequest $req, Menu $menu)
    {
        $data = $req->validated();
        $data['menu_id'] = $menu->id;
        $data['sort_order'] = $data['sort_order'] ?? (int) $menu->items()->max('sort_order') + 1;
        $data['parent_id'] = $this->sanitizeParentId($menu, $data['parent_id'] ?? null);
        MenuItem::create($data);
        return redirect()->route('admin.menus.edit', $menu)->with('success', 'Item added.');
    }

    public function edit(MenuItem $item)
    {
        $item->load('menu');
        // Root-level items in the same menu, excluding the row itself (a row
        // cannot be its own parent and we cap nesting at one level).
        $roots = $item->menu->items()
            ->whereNull('parent_id')
            ->where('id', '!=', $item->id)
            ->orderBy('sort_order')
            ->get();
        return view('admin.menus.item-edit', ['item' => $item, 'menu' => $item->menu, 'rootChoices' => $roots]);
    }

    public function update(MenuItemRequest $req, MenuItem $item)
    {
        $data = $req->validated();
        if (array_key_exists('parent_id', $data)) {
            $data['parent_id'] = $this->sanitizeParentId($item->menu, $data['parent_id'], $item->id);
        }
        $item->update($data);
        return redirect()->route('admin.menus.edit', $item->menu)->with('success', 'Item updated.');
    }

    /**
     * Reorder menu items + reparent them in bulk. Payload format:
     *   tree => [['id' => 1, 'parent_id' => null, 'sort_order' => 0], ...]
     * Two-or-more-deep nesting is collapsed to one level: anything whose
     * `parent_id` references another non-root row is forced to root.
     */
    public function reorder(Menu $menu, Request $req)
    {
        if (! $req->user('admin')?->can('manage-menus')) {
            abort(403);
        }

        $payload = $req->input('tree', []);
        if (! is_array($payload)) {
            return response()->json(['ok' => false, 'error' => 'invalid payload'], 422);
        }

        // First pass: collect ids and intended parent_ids so we can collapse
        // any 2+ deep nesting to a single level.
        $roots = [];
        foreach ($payload as $row) {
            if (! is_array($row) || ! isset($row['id'])) {
                continue;
            }
            if (($row['parent_id'] ?? null) === null) {
                $roots[(int) $row['id']] = true;
            }
        }

        $ids = collect($payload)->pluck('id')->filter()->map(fn ($v) => (int) $v)->all();
        $allowedIds = $menu->items()->whereIn('id', $ids)->pluck('id')->all();
        $allowedSet = array_flip($allowedIds);

        DB::transaction(function () use ($payload, $menu, $allowedSet, $roots) {
            foreach ($payload as $row) {
                if (! is_array($row) || ! isset($row['id'])) {
                    continue;
                }
                $id = (int) $row['id'];
                if (! isset($allowedSet[$id])) {
                    continue;
                }
                $parentId = $row['parent_id'] ?? null;
                $parentId = $parentId !== null ? (int) $parentId : null;

                // Collapse 2+ deep: parent must be a root or null.
                if ($parentId !== null && ! isset($roots[$parentId])) {
                    $parentId = null;
                }
                // Parent must belong to the same menu.
                if ($parentId !== null && ! isset($allowedSet[$parentId])) {
                    $parentId = null;
                }
                // Cannot parent to self.
                if ($parentId === $id) {
                    $parentId = null;
                }

                MenuItem::where('id', $id)->where('menu_id', $menu->id)->update([
                    'parent_id'  => $parentId,
                    'sort_order' => (int) ($row['sort_order'] ?? 0),
                ]);
            }
        });

        return response()->json(['ok' => true]);
    }

    /**
     * Ensure the chosen parent belongs to the same menu and isn't the row itself.
     * Also restricts to one level: parent must itself be a root (parent_id null).
     */
    protected function sanitizeParentId(Menu $menu, $parentId, ?int $selfId = null): ?int
    {
        if ($parentId === null || $parentId === '') {
            return null;
        }
        $parentId = (int) $parentId;
        if ($selfId !== null && $parentId === $selfId) {
            return null;
        }
        $parent = $menu->items()->find($parentId);
        if (! $parent || $parent->parent_id !== null) {
            return null;
        }
        return $parentId;
    }

    public function show(MenuItem $item)
    {
        return redirect()->route('admin.menus.edit', $item->menu);
    }

    public function destroy(MenuItem $item)
    {
        $menu = $item->menu;
        $item->delete();
        return redirect()->route('admin.menus.edit', $menu)->with('success', 'Item removed.');
    }
}
