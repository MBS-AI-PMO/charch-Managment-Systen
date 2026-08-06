<?php

use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\User;

it('lets an admin view the blog posts index', function () {
    $this->actingAs(makeAdmin(), 'admin')
        ->get(route('admin.blog.posts.index'))
        ->assertOk();
});

it('creates and updates a blog post', function () {
    $admin = makeAdmin();
    $cat = BlogCategory::factory()->create();

    $this->actingAs($admin, 'admin')
        ->post(route('admin.blog.posts.store'), [
            'title' => 'My First Post',
            'body' => '<p>Hello world.</p>',
            'category_id' => $cat->id,
            'is_published' => 1,
        ])->assertRedirect();

    $post = BlogPost::where('title', 'My First Post')->firstOrFail();
    expect($post->is_published)->toBeTrue()
        ->and($post->author_id)->toBe($admin->id);

    $this->actingAs($admin, 'admin')
        ->put(route('admin.blog.posts.update', $post), [
            'title' => 'My First Post (Updated)',
            'body' => '<p>Updated body.</p>',
            'is_published' => 1,
        ])->assertRedirect();

    expect($post->fresh()->title)->toBe('My First Post (Updated)');
});

it('purifies blog post bodies', function () {
    $admin = makeAdmin();
    $this->actingAs($admin, 'admin')
        ->post(route('admin.blog.posts.store'), [
            'title' => 'XSS attempt',
            'body' => '<script>alert(1)</script><p>safe</p>',
            'is_published' => 1,
        ])->assertRedirect();

    $body = BlogPost::where('title', 'XSS attempt')->firstOrFail()->body;
    expect($body)->not->toContain('<script>')
        ->and($body)->toContain('safe');
});

it('hides draft blog posts from the public', function () {
    $draft = BlogPost::factory()->draft()->create();

    $this->get(route('site.blog.show', $draft))->assertNotFound();
});

it('denies non-admins from the blog admin', function () {
    $admin = User::factory()->create(['is_admin' => true, 'password_change_required' => false]);
    $admin->assignRole('Event Organizer');

    $this->actingAs($admin, 'admin')
        ->get(route('admin.blog.posts.index'))
        ->assertStatus(403);
});
