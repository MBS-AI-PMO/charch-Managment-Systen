<?php

use App\Models\HeroSlide;
use App\Models\Page;
use Illuminate\Support\Facades\Schema;

beforeEach(function () {
    // Ensure a published home page exists so the controller can render.
    Page::query()->updateOrCreate(
        ['slug' => 'home'],
        [
            'title' => 'Welcome',
            'body' => 'A word of welcome.',
            'is_published' => true,
            'published_at' => now()->subDay(),
            'hero_heading' => 'Welcome home',
            'hero_subheading' => 'Glad you are here.',
        ],
    );
});

it('has the hero_slides table with expected columns', function () {
    expect(Schema::hasTable('hero_slides'))->toBeTrue()
        ->and(Schema::hasColumn('hero_slides', 'position'))->toBeTrue()
        ->and(Schema::hasColumn('hero_slides', 'image_path'))->toBeTrue()
        ->and(Schema::hasColumn('hero_slides', 'eyebrow'))->toBeTrue()
        ->and(Schema::hasColumn('hero_slides', 'heading'))->toBeTrue()
        ->and(Schema::hasColumn('hero_slides', 'sub'))->toBeTrue()
        ->and(Schema::hasColumn('hero_slides', 'primary_cta_url'))->toBeTrue()
        ->and(Schema::hasColumn('hero_slides', 'primary_cta_label'))->toBeTrue()
        ->and(Schema::hasColumn('hero_slides', 'secondary_cta_url'))->toBeTrue()
        ->and(Schema::hasColumn('hero_slides', 'secondary_cta_label'))->toBeTrue()
        ->and(Schema::hasColumn('hero_slides', 'is_active'))->toBeTrue();
});

it('renders all 3 active slide headings on the home page', function () {
    HeroSlide::factory()->create(['position' => 1, 'heading' => 'Welcome to the Family', 'is_active' => true]);
    HeroSlide::factory()->create(['position' => 2, 'heading' => 'Listen to Sundays Sermon', 'is_active' => true]);
    HeroSlide::factory()->create(['position' => 3, 'heading' => 'Join an Upcoming Event', 'is_active' => true]);

    $this->get(route('site.home'))
        ->assertOk()
        ->assertSee('Welcome to the Family')
        ->assertSee('Listen to Sundays Sermon')
        ->assertSee('Join an Upcoming Event');
});

it('excludes inactive slides from the carousel', function () {
    HeroSlide::factory()->create(['position' => 1, 'heading' => 'Visible Slide', 'is_active' => true]);
    HeroSlide::factory()->create(['position' => 2, 'heading' => 'Hidden Slide', 'is_active' => false]);

    $response = $this->get(route('site.home'))->assertOk();

    $response->assertSee('Visible Slide');
    $response->assertDontSee('Hidden Slide');
});

it('falls back to the static hero when no active slides exist', function () {
    HeroSlide::query()->delete();

    $this->get(route('site.home'))
        ->assertOk()
        ->assertSee('Welcome home');
});

it('emits Alpine x-data with currentSlide state when slides exist', function () {
    HeroSlide::factory()->create(['position' => 1, 'heading' => 'Slide One', 'is_active' => true]);
    HeroSlide::factory()->create(['position' => 2, 'heading' => 'Slide Two', 'is_active' => true]);

    $html = $this->get(route('site.home'))->assertOk()->getContent();

    expect($html)->toContain('x-data')
        ->and($html)->toContain('currentSlide');
});

it('orders slides by position', function () {
    HeroSlide::factory()->create(['position' => 3, 'heading' => 'Third Slot', 'is_active' => true]);
    HeroSlide::factory()->create(['position' => 1, 'heading' => 'First Slot', 'is_active' => true]);
    HeroSlide::factory()->create(['position' => 2, 'heading' => 'Second Slot', 'is_active' => true]);

    $html = $this->get(route('site.home'))->assertOk()->getContent();

    $posFirst = strpos($html, 'First Slot');
    $posSecond = strpos($html, 'Second Slot');
    $posThird = strpos($html, 'Third Slot');

    expect($posFirst)->toBeLessThan($posSecond)
        ->and($posSecond)->toBeLessThan($posThird);
});
