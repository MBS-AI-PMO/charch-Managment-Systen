<?php

use App\Models\Event;
use App\Models\EventAttendance;
use App\Models\EventRsvp;
use App\Models\User;

beforeEach(function () {
    // RolePermissionSeeder is already run by tests/Pest.php.

    $this->siteAdmin = User::factory()->create(['is_admin' => true]);
    $this->siteAdmin->assignRole('Site Admin');

    $this->organizer = User::factory()->create(['is_admin' => true]);
    $this->organizer->assignRole('Event Organizer');

    $this->event = Event::factory()->create(['organizer_id' => $this->organizer->id]);
});

it('regenerates check-in code', function () {
    $this->actingAs($this->siteAdmin, 'admin')
        ->post(route('admin.events.checkin-code.regenerate', $this->event))
        ->assertRedirect();

    expect($this->event->fresh()->checkin_code)->toMatch('/^\d{4}$/');
});

it('toggles attendance open and assigns code automatically', function () {
    expect($this->event->checkin_code)->toBeNull();

    $this->actingAs($this->siteAdmin, 'admin')
        ->put(route('admin.events.attendance.toggle', $this->event), ['open' => 1]);

    $fresh = $this->event->fresh();
    expect($fresh->attendance_open)->toBeTrue()
        ->and($fresh->checkin_code)->toMatch('/^\d{4}$/');
});

it('marks a member attendance via organizer', function () {
    $member = User::factory()->create();
    EventRsvp::factory()->create(['event_id' => $this->event->id, 'user_id' => $member->id]);

    $this->actingAs($this->organizer, 'admin')
        ->post(route('admin.events.attendance.store', $this->event), ['user_id' => $member->id]);

    expect(EventAttendance::where('event_id', $this->event->id)
        ->where('user_id', $member->id)
        ->where('method', 'organizer')
        ->where('recorded_by', $this->organizer->id)
        ->exists())->toBeTrue();
});

it('records a walk-in', function () {
    $this->actingAs($this->organizer, 'admin')
        ->post(route('admin.events.attendance.store', $this->event), [
            'guest_name' => 'Jane Doe',
            'guest_count' => 2,
        ]);

    expect(EventAttendance::where('guest_name', 'Jane Doe')->where('guest_count', 2)->exists())->toBeTrue();
});

it('forbids organizer from managing someone else\'s event', function () {
    $other = User::factory()->create(['is_admin' => true]);
    $other->assignRole('Event Organizer');
    $otherEvent = Event::factory()->create(['organizer_id' => $other->id]);

    $this->actingAs($this->organizer, 'admin')
        ->get(route('admin.events.attendance', $otherEvent))
        ->assertForbidden();
});

it('exports CSV with header row', function () {
    EventAttendance::factory()->create(['event_id' => $this->event->id]);

    $resp = $this->actingAs($this->siteAdmin, 'admin')
        ->get(route('admin.events.attendance.export', $this->event));

    $resp->assertOk();
    expect($resp->headers->get('Content-Type'))->toContain('text/csv');

    $content = $resp->streamedContent();
    expect($content)->toContain('Name,"RSVP Guests","Checked In",Method,"Checked In At",Note');
});

it('csv-safe prefixes injection characters', function () {
    $controller = new \App\Http\Controllers\Admin\AttendanceController();
    $ref = new ReflectionMethod($controller, 'csvSafe');
    $ref->setAccessible(true);
    expect($ref->invoke($controller, '=cmd|" /C calc"!A0'))->toStartWith("'=");
});
