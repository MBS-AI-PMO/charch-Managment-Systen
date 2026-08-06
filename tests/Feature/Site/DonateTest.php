<?php

use App\Models\Page;

it('renders the public /donate page with the seeded body content', function () {
    Page::create([
        'slug' => 'donate',
        'title' => 'Give',
        'body' => '<p>Bank: 12345678</p>',
        'is_published' => true,
        'published_at' => now()->subDay(),
    ]);

    $this->get('/donate')
        ->assertOk()
        ->assertSee('12345678');
});

it('redirects /member/giving to /donate', function () {
    Page::create([
        'slug' => 'donate',
        'title' => 'Give',
        'body' => '<p>Donate body.</p>',
        'is_published' => true,
        'published_at' => now()->subDay(),
    ]);

    $u = makeMember();

    $this->actingAs($u, 'web')
        ->get(route('member.giving'))
        ->assertRedirect(route('site.donate'));
});
