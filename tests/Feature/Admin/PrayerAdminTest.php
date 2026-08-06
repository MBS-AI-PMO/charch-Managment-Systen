<?php

use App\Models\ActivityLog;
use App\Models\PrayerRequest;
use App\Models\User;

it('lets a Site Admin list /admin/prayer-requests', function () {
    $admin = makeAdmin();

    $this->actingAs($admin, 'admin')
        ->get(route('admin.prayer-requests.index'))
        ->assertOk();
});

it('denies an Event Organizer (no manage-prayer-requests permission)', function () {
    $u = User::factory()->create(['is_admin' => true, 'password_change_required' => false]);
    $u->assignRole('Event Organizer');

    $this->actingAs($u, 'admin')
        ->get(route('admin.prayer-requests.index'))
        ->assertStatus(403);
});

it('writes an activity_log row when admin changes prayer request status', function () {
    $admin = makeAdmin();
    $owner = makeMember();
    $p = PrayerRequest::factory()->create(['user_id' => $owner->id, 'status' => 'pending']);

    $this->actingAs($admin, 'admin')
        ->put(route('admin.prayer-requests.update', $p), [
            'status' => 'praying',
        ])->assertRedirect();

    expect($p->fresh()->status)->toBe('praying');

    $log = ActivityLog::where('subject_type', PrayerRequest::class)
        ->where('subject_id', $p->id)
        ->where('action', 'updated')
        ->latest('id')
        ->first();

    expect($log)->not->toBeNull()
        ->and($log->user_id)->toBe($admin->id);
});
