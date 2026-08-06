<?php

use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\User;

/**
 * M8: drag-and-drop menu reordering + one-level submenu nesting.
 */

beforeEach(function () {
    $this->menu = Menu::create(['slug' => 'test-nav', 'name' => 'Test Nav']);

    $this->parent = MenuItem::create([
        'menu_id' => $this->menu->id,
        'label' => 'About',
        'link_type' => 'url',
        'link_value' => '/about',
        'target' => '_self',
        'sort_order' => 0,
    ]);

    $this->sibling = MenuItem::create([
        'menu_id' => $this->menu->id,
        'label' => 'Contact',
        'link_type' => 'url',
        'link_value' => '/contact',
        'target' => '_self',
        'sort_order' => 1,
    ]);
});

it('nests a menu item under another via parent_id', function () {
    $admin = makeAdmin();

    $this->actingAs($admin, 'admin')
        ->post(route('admin.menus.items.store', $this->menu), [
            'label' => 'Beliefs',
            'link_type' => 'url',
            'link_value' => '/beliefs',
            'parent_id' => $this->parent->id,
            'target' => '_self',
        ])
        ->assertRedirect();

    $child = MenuItem::where('label', 'Beliefs')->firstOrFail();
    expect($child->parent_id)->toBe($this->parent->id);
    expect($this->parent->children()->pluck('id')->all())->toBe([$child->id]);
});

it('updates sort_order and parent_id via the reorder endpoint', function () {
    $admin = makeAdmin();

    $child = MenuItem::create([
        'menu_id' => $this->menu->id,
        'label' => 'Beliefs',
        'link_type' => 'url',
        'link_value' => '/beliefs',
        'sort_order' => 2,
    ]);

    $this->actingAs($admin, 'admin')
        ->postJson(route('admin.menus.reorder', $this->menu), [
            'tree' => [
                ['id' => $this->sibling->id, 'parent_id' => null, 'sort_order' => 0],
                ['id' => $this->parent->id, 'parent_id' => null, 'sort_order' => 1],
                ['id' => $child->id, 'parent_id' => $this->parent->id, 'sort_order' => 0],
            ],
        ])
        ->assertOk()
        ->assertJson(['ok' => true]);

    expect($this->sibling->fresh()->sort_order)->toBe(0);
    expect($this->parent->fresh()->sort_order)->toBe(1);
    expect($child->fresh()->parent_id)->toBe($this->parent->id);
    expect($child->fresh()->sort_order)->toBe(0);
});

it('collapses two-level deep nesting to one level on reorder', function () {
    $admin = makeAdmin();

    $child = MenuItem::create([
        'menu_id' => $this->menu->id,
        'label' => 'Beliefs',
        'link_type' => 'url',
        'link_value' => '/beliefs',
        'sort_order' => 0,
    ]);
    $grandchild = MenuItem::create([
        'menu_id' => $this->menu->id,
        'label' => 'Statement of Faith',
        'link_type' => 'url',
        'link_value' => '/faith',
        'sort_order' => 0,
    ]);

    $this->actingAs($admin, 'admin')
        ->postJson(route('admin.menus.reorder', $this->menu), [
            'tree' => [
                ['id' => $this->parent->id, 'parent_id' => null, 'sort_order' => 0],
                ['id' => $child->id, 'parent_id' => $this->parent->id, 'sort_order' => 0],
                // grandchild is parented under a non-root → should be forced to root.
                ['id' => $grandchild->id, 'parent_id' => $child->id, 'sort_order' => 0],
            ],
        ])
        ->assertOk();

    expect($grandchild->fresh()->parent_id)->toBeNull();
});

it('forbids the reorder endpoint for guests and non-admins', function () {
    // Guest → 401/redirect (auth:admin middleware).
    $this->postJson(route('admin.menus.reorder', $this->menu), ['tree' => []])
        ->assertStatus(401);

    // Non-admin web user → admin guard rejects (401 for JSON requests).
    $member = makeMember();
    $this->actingAs($member, 'web')
        ->postJson(route('admin.menus.reorder', $this->menu), ['tree' => []])
        ->assertStatus(401);

    // Admin without manage-menus permission → 403.
    $organizer = User::factory()->create(['is_admin' => true, 'password_change_required' => false]);
    $organizer->assignRole('Event Organizer');
    $this->actingAs($organizer, 'admin')
        ->postJson(route('admin.menus.reorder', $this->menu), ['tree' => []])
        ->assertStatus(403);
});

it('exposes a roots() scope that returns only top-level items', function () {
    $child = MenuItem::create([
        'menu_id' => $this->menu->id,
        'label' => 'Beliefs',
        'link_type' => 'url',
        'link_value' => '/beliefs',
        'parent_id' => $this->parent->id,
        'sort_order' => 0,
    ]);

    $roots = MenuItem::query()->where('menu_id', $this->menu->id)->roots()->pluck('id')->all();

    expect($roots)->toContain($this->parent->id, $this->sibling->id);
    expect($roots)->not->toContain($child->id);
});

it('renders a child item inside its parent dropdown on a public page', function () {
    // Wire two items into the seeded main nav: a parent + a child.
    $main = Menu::firstOrCreate(['slug' => 'main'], ['name' => 'Main Navigation']);
    // Wipe and re-seed so this test is independent of seeder ordering.
    $main->items()->delete();
    $parent = MenuItem::create([
        'menu_id' => $main->id,
        'label' => 'AboutUs',
        'link_type' => 'url',
        'link_value' => '/about',
        'sort_order' => 0,
    ]);
    MenuItem::create([
        'menu_id' => $main->id,
        'label' => 'OurBeliefs',
        'link_type' => 'url',
        'link_value' => '/beliefs',
        'parent_id' => $parent->id,
        'sort_order' => 0,
    ]);

    // Use /contact — it doesn't require a published Page row.
    $this->get('/contact')
        ->assertOk()
        ->assertSeeInOrder(['AboutUs', 'OurBeliefs']);
});
