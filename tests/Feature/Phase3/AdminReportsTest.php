<?php

use App\Models\Event;
use App\Models\EventAttendance;
use App\Models\Tithe;
use App\Models\TitheFund;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

beforeEach(function () {
    // Pest auto-seeds RolePermissionSeeder via tests/Pest.php. We still need
    // the default tithe funds for these tests.
    $this->seed(\Database\Seeders\TitheFundSeeder::class);

    Cache::flush();

    $this->siteAdmin = User::factory()->create([
        'is_admin' => true,
        'password_change_required' => false,
    ]);
    $this->siteAdmin->assignRole('Site Admin');

    $this->organizer = User::factory()->create([
        'is_admin' => true,
        'password_change_required' => false,
    ]);
    $this->organizer->assignRole('Event Organizer'); // no view-reports
});

it('renders the reports dashboard for someone with view-reports', function () {
    $this->actingAs($this->siteAdmin, 'admin')
        ->get(route('admin.reports.index'))
        ->assertOk()
        ->assertSee('Members')
        ->assertSee('Events held')
        ->assertSee('Giving');
});

it('forbids Event Organizer (no view-reports)', function () {
    $this->actingAs($this->organizer, 'admin')
        ->get(route('admin.reports.index'))
        ->assertForbidden();
});

it('counts giving total in cents from tithes within range', function () {
    $fund = TitheFund::where('slug', 'general')->first();
    Tithe::factory()->create([
        'fund_id' => $fund->id,
        'amount_cents' => 5000,
        'received_at' => now()->subDays(5),
    ]);
    Tithe::factory()->create([
        'fund_id' => $fund->id,
        'amount_cents' => 2500,
        'received_at' => now()->subDays(40), // outside default 30d range
    ]);

    $this->actingAs($this->siteAdmin, 'admin')
        ->get(route('admin.reports.index'))
        ->assertSee('$50.00')
        ->assertDontSee('$75.00');
});

it('streams attendance CSV', function () {
    Event::factory()->create(['title' => 'Sunday Service', 'starts_at' => now()->subDay()]);

    $resp = $this->actingAs($this->siteAdmin, 'admin')
        ->get(route('admin.reports.attendance.csv'));

    $resp->assertOk();
    expect($resp->headers->get('Content-Type'))->toContain('text/csv');
    expect($resp->streamedContent())->toContain('Event,Date,RSVPs,"Checked In",Walk-ins');
});

it('streams giving CSV', function () {
    $resp = $this->actingAs($this->siteAdmin, 'admin')
        ->get(route('admin.reports.giving.csv'));

    $resp->assertOk();
    expect($resp->streamedContent())->toContain('Date,Fund,Method,Amount');
});

it('respects custom date range', function () {
    $fund = TitheFund::where('slug', 'general')->first();
    Tithe::factory()->create([
        'fund_id' => $fund->id,
        'amount_cents' => 99900,
        'received_at' => now()->subDays(120),
    ]);

    $this->actingAs($this->siteAdmin, 'admin')
        ->get(route('admin.reports.index', [
            'from' => now()->subDays(130)->toDateString(),
            'to' => now()->subDays(100)->toDateString(),
        ]))
        ->assertSee('$999.00');
});
