<?php

use App\Models\User;

it('shows the registration form', function () {
    $this->get('/register')
        ->assertOk()
        ->assertSee('account', false);
});

it('registers a new member and redirects to the verification notice', function () {
    $resp = $this->post('/register', [
        'name' => 'Jane Doe',
        'email' => 'jane@example.test',
        'password' => 'memberPass-1!',
        'password_confirmation' => 'memberPass-1!',
    ]);

    $resp->assertRedirect(route('verification.notice'));

    $u = User::where('email', 'jane@example.test')->first();
    expect($u)->not->toBeNull()
        ->and($u->is_admin)->toBeFalse()
        ->and($u->email_verified_at)->toBeNull();
});

it('silently drops bot submissions caught by the honeypot', function () {
    // The honeypot field is validated `size:0`; bots that fill it bounce off
    // validation before ever creating a user.
    $resp = $this->post('/register', [
        'name' => 'Bot',
        'email' => 'bot@example.test',
        'password' => 'memberPass-1!',
        'password_confirmation' => 'memberPass-1!',
        'website' => 'http://spam.test',
    ]);

    expect(User::where('email', 'bot@example.test')->exists())->toBeFalse();
    $resp->assertSessionHasErrors('website');
});

it('rejects a duplicate email address', function () {
    User::factory()->create(['email' => 'dupe@example.test']);

    $this->post('/register', [
        'name' => 'Dup',
        'email' => 'dupe@example.test',
        'password' => 'memberPass-1!',
        'password_confirmation' => 'memberPass-1!',
    ])->assertSessionHasErrors('email');
});
