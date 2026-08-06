<?php

use App\Models\FeedPost;
use App\Models\FeedReaction;

it('renders the community feed for a verified member', function () {
    $u = makeMember();
    FeedPost::factory()->create();

    $this->actingAs($u, 'web')
        ->get(route('member.feed.index'))
        ->assertOk();
});

it('creates a heart reaction when a member reacts', function () {
    $u = makeMember();
    $post = FeedPost::factory()->create();

    $this->actingAs($u, 'web')
        ->post(route('member.feed.react', $post), ['kind' => 'heart'])
        ->assertRedirect();

    expect(FeedReaction::where('feed_post_id', $post->id)->where('user_id', $u->id)->count())->toBe(1)
        ->and(FeedReaction::where('feed_post_id', $post->id)->where('user_id', $u->id)->value('kind'))->toBe('heart');
});

it('changes kind from heart to pray without creating a second row', function () {
    $u = makeMember();
    $post = FeedPost::factory()->create();

    $this->actingAs($u, 'web')->post(route('member.feed.react', $post), ['kind' => 'heart']);
    $this->actingAs($u, 'web')->post(route('member.feed.react', $post), ['kind' => 'pray']);

    expect(FeedReaction::where('feed_post_id', $post->id)->where('user_id', $u->id)->count())->toBe(1)
        ->and(FeedReaction::where('feed_post_id', $post->id)->where('user_id', $u->id)->value('kind'))->toBe('pray');
});

it('toggles the same reaction off when sent twice', function () {
    $u = makeMember();
    $post = FeedPost::factory()->create();

    $this->actingAs($u, 'web')->post(route('member.feed.react', $post), ['kind' => 'heart']);
    $this->actingAs($u, 'web')->post(route('member.feed.react', $post), ['kind' => 'heart']);

    expect(FeedReaction::where('feed_post_id', $post->id)->where('user_id', $u->id)->count())->toBe(0);
});

it('orders pinned posts ahead of unpinned ones', function () {
    $oldUnpinned = FeedPost::factory()->create([
        'title' => 'Old unpinned post',
        'pinned' => false,
        'published_at' => now()->subDays(5),
    ]);
    $newPinned = FeedPost::factory()->create([
        'title' => 'Pinned announcement',
        'pinned' => true,
        'published_at' => now()->subHour(),
    ]);
    $newUnpinned = FeedPost::factory()->create([
        'title' => 'Fresh unpinned post',
        'pinned' => false,
        'published_at' => now()->subMinutes(5),
    ]);

    $u = makeMember();
    $resp = $this->actingAs($u, 'web')->get(route('member.feed.index'));
    $resp->assertOk();

    $body = $resp->getContent();
    $pinPos = strpos($body, 'Pinned announcement');
    $freshPos = strpos($body, 'Fresh unpinned post');

    expect($pinPos)->not->toBeFalse()
        ->and($freshPos)->not->toBeFalse()
        ->and($pinPos < $freshPos)->toBeTrue();
});
