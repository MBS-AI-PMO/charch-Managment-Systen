<?php

use App\Models\BlogPost;
use App\Models\Event;
use App\Models\Ministry;
use App\Models\Page;
use App\Models\Sermon;

beforeEach(function () {
    Page::factory()->create(['slug' => 'home']);
    Page::factory()->create(['slug' => 'about-us']);
    Page::factory()->create(['slug' => 'sermons']);
    Page::factory()->create(['slug' => 'events']);
    Page::factory()->create(['slug' => 'ministries']);
    Page::factory()->create(['slug' => 'blog']);
    Page::factory()->create(['slug' => 'contact-us']);
});

it('renders the home page', function () {
    Event::factory()->create(['starts_at' => now()->addDay()]);
    Sermon::factory()->create();
    BlogPost::factory()->create();
    Ministry::factory()->create();

    $this->get('/')->assertOk();
});

it('renders the about page', function () {
    $this->get('/about')->assertOk();
});

it('renders the sermons index', function () {
    Sermon::factory()->create();

    $this->get('/sermons')->assertOk();
});

it('renders a sermon detail', function () {
    $sermon = Sermon::factory()->create();

    $this->get(route('site.sermons.show', $sermon))->assertOk();
});

it('renders the events index', function () {
    Event::factory()->create();

    $this->get('/events')->assertOk();
});

it('renders an event detail', function () {
    $event = Event::factory()->create();

    $this->get(route('site.events.show', $event))->assertOk();
});

it('renders the ministries index', function () {
    Ministry::factory()->create();

    $this->get('/ministries')->assertOk();
});

it('renders a ministry detail', function () {
    $ministry = Ministry::factory()->create();

    $this->get(route('site.ministries.show', $ministry))->assertOk();
});

it('renders the blog index', function () {
    BlogPost::factory()->create();

    $this->get('/blog')->assertOk();
});

it('renders a blog post', function () {
    $post = BlogPost::factory()->create();

    $this->get(route('site.blog.show', $post))->assertOk();
});

it('renders the contact page', function () {
    $this->get('/contact')->assertOk();
});

it('returns 404 for an unpublished blog post', function () {
    $post = BlogPost::factory()->draft()->create();
    $this->get(route('site.blog.show', $post))->assertNotFound();
});

it('returns 404 for an unpublished sermon', function () {
    $sermon = Sermon::factory()->draft()->create();
    $this->get(route('site.sermons.show', $sermon))->assertNotFound();
});

it('returns 404 for an unpublished ministry', function () {
    $ministry = Ministry::factory()->draft()->create();
    $this->get(route('site.ministries.show', $ministry))->assertNotFound();
});
