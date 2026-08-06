<?php

use App\Models\Tithe;
use App\Models\TitheFund;
use App\Models\User;

beforeEach(function () {
    // Pest auto-seeds RolePermissionSeeder via tests/Pest.php. We still need
    // the default tithe funds for these tests.
    $this->seed(\Database\Seeders\TitheFundSeeder::class);

    $this->siteAdmin = User::factory()->create([
        'is_admin' => true,
        'password_change_required' => false,
    ]);
    $this->siteAdmin->assignRole('Site Admin');

    $this->organizer = User::factory()->create([
        'is_admin' => true,
        'password_change_required' => false,
    ]);
    $this->organizer->assignRole('Event Organizer');

    $this->fund = TitheFund::where('slug', 'general')->first();
});

it('records a member tithe and stores amount as cents', function () {
    $member = User::factory()->create(['name' => 'Sarah Chen']);

    $this->actingAs($this->siteAdmin, 'admin')
        ->post(route('admin.tithes.store'), [
            'user_id' => $member->id,
            'fund_id' => $this->fund->id,
            'amount' => '50.00',
            'method' => 'cash',
            'received_at' => now()->toDateString(),
        ])
        ->assertRedirect(route('admin.tithes.index'));

    $tithe = Tithe::first();
    expect($tithe->amount_cents)->toBe(5000)
        ->and($tithe->giver_name)->toBe('Sarah Chen')
        ->and($tithe->user_id)->toBe($member->id);
});

it('records an anonymous tithe with giver_name only', function () {
    $this->actingAs($this->siteAdmin, 'admin')
        ->post(route('admin.tithes.store'), [
            'user_id' => null,
            'giver_name' => 'Anonymous',
            'fund_id' => $this->fund->id,
            'amount' => '100',
            'method' => 'bank_transfer',
            'received_at' => now()->toDateString(),
            'reference' => 'BANK-001',
        ]);

    $tithe = Tithe::first();
    expect($tithe->user_id)->toBeNull()
        ->and($tithe->giver_name)->toBe('Anonymous')
        ->and($tithe->reference)->toBe('BANK-001');
});

it('forbids Event Organizer from accessing tithes', function () {
    $this->actingAs($this->organizer, 'admin')
        ->get(route('admin.tithes.index'))
        ->assertForbidden();
});

it('member lookup returns id+name only', function () {
    User::factory()->create(['name' => 'Sarah Chen', 'email' => 'sarah@example.com']);

    $resp = $this->actingAs($this->siteAdmin, 'admin')
        ->getJson(route('admin.tithes.member-lookup', ['q' => 'Sarah']));

    $resp->assertOk();
    $row = $resp->json(0);
    expect(array_keys($row))->toBe(['id', 'name']);
});

it('member lookup needs 2+ chars', function () {
    User::factory()->create(['name' => 'Aaron']);

    $this->actingAs($this->siteAdmin, 'admin')
        ->getJson(route('admin.tithes.member-lookup', ['q' => 'A']))
        ->assertOk()
        ->assertExactJson([]);
});

it('deactivates fund instead of deleting when tithes reference it', function () {
    Tithe::factory()->create(['fund_id' => $this->fund->id]);

    $this->actingAs($this->siteAdmin, 'admin')
        ->delete(route('admin.tithes.funds.destroy', $this->fund))
        ->assertRedirect();

    expect($this->fund->fresh()->is_active)->toBeFalse()
        ->and(TitheFund::find($this->fund->id))->not->toBeNull();
});

it('hard-deletes fund when no tithes reference it', function () {
    $fund = TitheFund::factory()->create();

    $this->actingAs($this->siteAdmin, 'admin')
        ->delete(route('admin.tithes.funds.destroy', $fund));

    expect(TitheFund::find($fund->id))->toBeNull();
});

it('purifies note input', function () {
    $this->actingAs($this->siteAdmin, 'admin')
        ->post(route('admin.tithes.store'), [
            'fund_id' => $this->fund->id,
            'amount' => '10',
            'method' => 'cash',
            'received_at' => now()->toDateString(),
            'giver_name' => 'Bob',
            'note' => '<script>alert("xss")</script><b>bold</b>',
        ]);

    expect(Tithe::first()->note)->not->toContain('<script>')->toContain('<b>bold</b>');
});
