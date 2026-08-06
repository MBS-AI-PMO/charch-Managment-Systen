<?php

use Illuminate\Support\Facades\RateLimiter;

beforeEach(function () {
    RateLimiter::clear('admin-login');
});

it('blocks admin login after 5 failed attempts', function () {
    $admin = makeAdmin();

    foreach (range(1, 5) as $_) {
        $this->post('/admin/login', [
            'email' => $admin->email,
            'password' => 'wrong-password',
        ])->assertSessionHasErrors('email');
    }

    // 6th attempt should hit the throttle middleware and either return 429
    // or surface a throttle-flavoured validation error on the email field.
    $resp = $this->post('/admin/login', [
        'email' => $admin->email,
        'password' => 'wrong-password',
    ]);

    $status = $resp->getStatusCode();
    if ($status === 429) {
        expect($status)->toBe(429);
    } else {
        $resp->assertSessionHasErrors('email');
        $msg = session('errors')->get('email')[0] ?? '';
        expect(strtolower($msg))->toContain('too many');
    }
});

it('throttles the contact form after 5 hourly submissions', function () {
    // Seed 5 successful submissions from the same IP.
    foreach (range(1, 5) as $i) {
        $this->post('/contact', [
            'name' => 'Test',
            'email' => 'visitor'.$i.'@example.com',
            'subject' => 'Hello',
            'message' => 'Just saying hi.',
        ])->assertRedirect();
    }

    $this->post('/contact', [
        'name' => 'Test',
        'email' => 'sixth@example.com',
        'subject' => 'Hello',
        'message' => 'This one should be rate limited.',
    ])->assertStatus(429);
});
