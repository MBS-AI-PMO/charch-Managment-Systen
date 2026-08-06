<?php

use App\Jobs\SendFeedBroadcast;
use App\Models\FeedPost;
use App\Models\User;
use Illuminate\Support\Facades\Bus;

it('lets a Site Admin create a feed post', function () {
    $admin = makeAdmin();

    $this->actingAs($admin, 'admin')
        ->post(route('admin.feed.store'), [
            'title' => 'Welcome back to church',
            'body' => '<p>Service times resume Sunday.</p>',
        ])->assertRedirect(route('admin.feed.index'));

    $post = FeedPost::where('title', 'Welcome back to church')->first();
    expect($post)->not->toBeNull()
        ->and($post->author_id)->toBe($admin->id);
});

it('dispatches SendFeedBroadcast when broadcast=1 is set on store', function () {
    Bus::fake();
    $admin = makeAdmin();

    $this->actingAs($admin, 'admin')
        ->post(route('admin.feed.store'), [
            'title' => 'Big announcement',
            'body' => '<p>Read me.</p>',
            'broadcast' => 1,
        ])->assertRedirect();

    Bus::assertDispatched(SendFeedBroadcast::class);
});

it('denies admins without manage-community-feed permission', function () {
    $u = User::factory()->create(['is_admin' => true, 'password_change_required' => false]);
    $u->assignRole('Event Organizer');

    $this->actingAs($u, 'admin')
        ->get(route('admin.feed.index'))
        ->assertStatus(403);
});
