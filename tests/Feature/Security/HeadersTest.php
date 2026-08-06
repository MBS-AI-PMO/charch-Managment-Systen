<?php

use App\Models\Page;

it('returns the expected security headers on the home page', function () {
    Page::factory()->create(['slug' => 'home']);

    $resp = $this->get('/');
    $resp->assertOk();
    $resp->assertHeader('X-Frame-Options', 'DENY');
    $resp->assertHeader('X-Content-Type-Options', 'nosniff');
    $resp->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');

    expect($resp->headers->get('Content-Security-Policy'))
        ->toContain("default-src 'self'")
        ->toContain("frame-ancestors 'none'");
});
