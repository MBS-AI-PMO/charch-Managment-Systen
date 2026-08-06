<?php

use App\Models\BlogPost;

it('strips script and event-handler attributes from a saved blog post body', function () {
    $admin = makeAdmin();
    $post = BlogPost::factory()->create();

    $this->actingAs($admin, 'admin')
        ->put(route('admin.blog.posts.update', $post), [
            'title' => 'XSS test',
            'body' => '<p onload="evil()">ok</p><script>alert(1)</script><img src=x onerror="bad()">',
            'is_published' => 1,
        ])->assertRedirect();

    $body = $post->fresh()->body;

    expect($body)->not->toContain('<script')
        ->and($body)->not->toContain('onerror')
        ->and($body)->not->toContain('onload')
        ->and($body)->toContain('ok');
});
