<?php

use App\Models\User;
use Illuminate\Support\Facades\URL;

it('redirects unverified members from /member to the verification notice', function () {
    $u = User::factory()->unverified()->create(['is_admin' => false, 'password_change_required' => false]);

    $this->actingAs($u, 'web')
        ->get('/member')
        ->assertRedirect(route('verification.notice'));
});

it('marks email verified when visiting a signed verification URL', function () {
    $u = User::factory()->unverified()->create(['is_admin' => false, 'password_change_required' => false]);

    $url = URL::temporarySignedRoute(
        'verification.verify',
        now()->addHour(),
        ['id' => $u->id, 'hash' => sha1($u->email)],
    );

    $this->actingAs($u, 'web')
        ->get($url)
        ->assertRedirect(route('member.dashboard'));

    expect($u->fresh()->hasVerifiedEmail())->toBeTrue();
});

it('rejects a tampered verification hash', function () {
    $u = User::factory()->unverified()->create(['is_admin' => false, 'password_change_required' => false]);

    $url = URL::temporarySignedRoute(
        'verification.verify',
        now()->addHour(),
        ['id' => $u->id, 'hash' => sha1('not-the-email@bad.test')],
    );

    $this->actingAs($u, 'web')
        ->get($url)
        ->assertForbidden();

    expect($u->fresh()->hasVerifiedEmail())->toBeFalse();
});
