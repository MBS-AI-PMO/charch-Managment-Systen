<?php

use App\Models\BlogPost;
use App\Models\ChurchBranch;
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

it('renders the our churches page', function () {
    Page::factory()->create(['slug' => 'our-churches']);
    ChurchBranch::create([
        'slug' => 'aog-church-zafar-town',
        'name' => 'AOG Church Zafar Town',
        'city' => 'Zafar Town, Lahore',
        'role' => 'Main campus',
        'is_published' => true,
        'sort_order' => 1,
    ]);
    ChurchBranch::create([
        'slug' => 'aog-church-razzaq-town',
        'name' => 'AOG Church Razzaq Town',
        'city' => 'Razzaq Town',
        'role' => 'Branch',
        'is_published' => true,
        'sort_order' => 2,
    ]);

    $this->get('/our-churches')
        ->assertOk()
        ->assertSee('Our Churches')
        ->assertSee('AOG Church Zafar Town')
        ->assertSee('AOG Church Razzaq Town');
});

it('renders a church detail page', function () {
    Page::factory()->create(['slug' => 'our-churches']);
    ChurchBranch::create([
        'slug' => 'aog-church-zafar-town',
        'name' => 'AOG Church Zafar Town',
        'address' => 'Street 4, Zafar Town, Lahore',
        'is_published' => true,
        'sort_order' => 1,
    ]);
    ChurchBranch::create([
        'slug' => 'aog-church-razzaq-town',
        'name' => 'AOG Church Razzaq Town',
        'is_published' => true,
        'sort_order' => 2,
    ]);

    $this->get('/our-churches/aog-church-zafar-town')
        ->assertOk()
        ->assertSee('AOG Church Zafar Town')
        ->assertSee('Street 4, Zafar Town, Lahore');

    $this->get('/our-churches/aog-church-razzaq-town')
        ->assertOk()
        ->assertSee('AOG Church Razzaq Town');
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

    $this->get('/news')->assertOk();
});

it('renders a blog post', function () {
    $post = BlogPost::factory()->create();

    $this->get(route('site.blog.show', $post))->assertOk();
});

it('renders the contact page', function () {
    $this->get('/contact')->assertOk();
});

it('renders the gallery page', function () {
    $this->get('/gallery')->assertOk()->assertSee('Gallery');
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
