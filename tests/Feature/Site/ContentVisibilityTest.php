<?php

use App\Models\BlogPost;
use App\Models\Event;
use App\Models\Ministry;
use App\Models\Page;

beforeEach(function () {
    Page::factory()->create(['slug' => 'events']);
    Page::factory()->create(['slug' => 'blog']);
});

it('hides a draft blog post and shows a published one', function () {
    $draft = BlogPost::factory()->draft()->create();
    $live = BlogPost::factory()->create();

    $this->get(route('site.blog.show', $draft))->assertNotFound();
    $this->get(route('site.blog.show', $live))->assertOk();
});

it('still surfaces past events under the Past tab', function () {
    Event::factory()->past()->create(['title' => 'Old Event']);
    Event::factory()->create(['title' => 'Future Event']);

    $resp = $this->get('/events?past_page=1');
    $resp->assertOk();
    expect($resp->getContent())->toContain('Old Event');
});

it('returns 404 for an unpublished ministry', function () {
    $ministry = Ministry::factory()->draft()->create();
    $this->get(route('site.ministries.show', $ministry))->assertNotFound();
});
