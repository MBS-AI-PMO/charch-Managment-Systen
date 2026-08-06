<?php

use App\Models\PrayerRequest;
use App\Models\PrayerRequestPray;

it('lets a member create a prayer request', function () {
    $u = makeMember();

    $this->actingAs($u, 'web')
        ->post(route('member.prayer.store'), [
            'title' => 'Pray for healing',
            'body' => 'My uncle is in hospital.',
            'is_public' => 1,
        ])->assertRedirect(route('member.prayer.index'));

    $p = PrayerRequest::where('title', 'Pray for healing')->first();
    expect($p)->not->toBeNull()
        ->and($p->user_id)->toBe($u->id)
        ->and($p->is_public)->toBeTrue()
        ->and($p->status)->toBe('pending');
});

it('forbids viewing another member\'s private prayer request', function () {
    $owner = makeMember();
    $other = makeMember();
    $p = PrayerRequest::factory()->create([
        'user_id' => $owner->id,
        'is_public' => false,
    ]);

    $this->actingAs($other, 'web')
        ->get(route('member.prayer.show', $p))
        ->assertForbidden();
});

it('shows "Anonymous" on the community board for anonymous requests', function () {
    $owner = makeMember(['name' => 'Real Name McUser']);
    $viewer = makeMember();
    PrayerRequest::factory()->create([
        'user_id' => $owner->id,
        'name' => $owner->name,
        'title' => 'Anonymous prayer entry',
        'is_public' => true,
        'is_anonymous' => true,
    ]);

    $resp = $this->actingAs($viewer, 'web')->get(route('member.prayer.index'));

    $resp->assertOk()
        ->assertSee('Anonymous')
        ->assertDontSee('Real Name McUser');
});

it('increments pray_count when toggling pray on, then decrements on toggle off', function () {
    $owner = makeMember();
    $prayer = PrayerRequest::factory()->create([
        'user_id' => $owner->id,
        'is_public' => true,
        'pray_count' => 0,
    ]);

    $u = makeMember();

    $this->actingAs($u, 'web')
        ->post(route('member.prayer.pray', $prayer))
        ->assertRedirect();

    expect($prayer->fresh()->pray_count)->toBe(1)
        ->and(PrayerRequestPray::where('prayer_request_id', $prayer->id)->where('user_id', $u->id)->exists())->toBeTrue();

    $this->actingAs($u, 'web')
        ->post(route('member.prayer.pray', $prayer))
        ->assertRedirect();

    expect($prayer->fresh()->pray_count)->toBe(0)
        ->and(PrayerRequestPray::where('prayer_request_id', $prayer->id)->where('user_id', $u->id)->exists())->toBeFalse();
});

it('lets an owner soft-delete their own prayer request', function () {
    $owner = makeMember();
    $p = PrayerRequest::factory()->create(['user_id' => $owner->id]);

    $this->actingAs($owner, 'web')
        ->delete(route('member.prayer.destroy', $p))
        ->assertRedirect(route('member.prayer.index'));

    expect(PrayerRequest::find($p->id))->toBeNull()
        ->and(PrayerRequest::withTrashed()->find($p->id))->not->toBeNull();
});

it('keeps non-public requests off the community board', function () {
    $owner = makeMember();
    $viewer = makeMember();
    PrayerRequest::factory()->create([
        'user_id' => $owner->id,
        'title' => 'Secret request title XYZ',
        'is_public' => false,
        'is_anonymous' => false,
    ]);

    $this->actingAs($viewer, 'web')
        ->get(route('member.prayer.index'))
        ->assertOk()
        ->assertDontSee('Secret request title XYZ');
});
