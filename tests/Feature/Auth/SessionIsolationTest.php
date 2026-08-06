<?php

it('keeps admin and member sessions independent', function () {
    $admin = makeAdmin();
    $member = makeMember();

    // Authenticate against both guards in the same test container.
    auth('admin')->login($admin);
    auth('web')->login($member);

    expect(auth('admin')->check())->toBeTrue()
        ->and(auth('admin')->id())->toBe($admin->id)
        ->and(auth('web')->check())->toBeTrue()
        ->and(auth('web')->id())->toBe($member->id);

    // Logging out the admin guard must not log out the web (member) guard.
    auth('admin')->logout();

    expect(auth('admin')->check())->toBeFalse()
        ->and(auth('web')->check())->toBeTrue()
        ->and(auth('web')->id())->toBe($member->id);
});
