<?php

use App\Http\Controllers\Site\BlogController;
use App\Http\Controllers\Site\ContactController;
use App\Http\Controllers\Site\EventController;
use App\Http\Controllers\Site\GalleryController;
use App\Http\Controllers\Site\MinistryController;
use App\Http\Controllers\Site\PageController;
use App\Http\Controllers\Site\QaController;
use App\Http\Controllers\Site\SermonController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
 
Route::get('/create-storage-link', function () {
    Artisan::call('storage:link');
 
    return "Storage link created successfully";
});

require __DIR__.'/auth.php';
require __DIR__.'/admin.php';
require __DIR__.'/member.php';

/*
|--------------------------------------------------------------------------
| Legacy / mistaken links (route names used as URLs)
|--------------------------------------------------------------------------
*/
Route::permanentRedirect('/site.home', '/');
Route::permanentRedirect('/site.about', '/about');
Route::permanentRedirect('/about-us', '/about');
Route::permanentRedirect('/site.contact', '/contact');
Route::permanentRedirect('/site.donate', '/donate');
Route::permanentRedirect('/site.sermons.index', '/sermons');
Route::permanentRedirect('/site.events.index', '/events');
Route::permanentRedirect('/site.ministries.index', '/ministries');
Route::permanentRedirect('/site.blog.index', '/news');
Route::permanentRedirect('/blog', '/news');
Route::permanentRedirect('/blog/{post}', '/news/{post}');

/*
|--------------------------------------------------------------------------
| Public site routes (M6)
|--------------------------------------------------------------------------
| All public-facing pages live under the `site.` route-name prefix. Menu
| items in the database reference these route names via link_type=route.
*/
Route::name('site.')->group(function () {
    Route::get('/', [PageController::class, 'home'])->name('home');
    Route::get('/about', [PageController::class, 'about'])->name('about');
    Route::get('/our-churches', [PageController::class, 'churches'])->name('churches');
    Route::get('/donate', [PageController::class, 'donate'])->name('donate');

    Route::get('/contact', [ContactController::class, 'show'])->name('contact');
    Route::post('/contact', [ContactController::class, 'submit'])
        ->name('contact.submit')
        ->middleware('throttle:contact-form');

    Route::get('/sermons', [SermonController::class, 'index'])->name('sermons.index');
    Route::get('/sermons/{sermon:slug}', [SermonController::class, 'show'])->name('sermons.show');

    Route::get('/gallery', [GalleryController::class, 'index'])->name('gallery.index');
    Route::get('/gallery/{folder:slug}', [GalleryController::class, 'show'])->name('gallery.show');

    Route::get('/events', [EventController::class, 'index'])->name('events.index');
    Route::get('/events/{event:slug}', [EventController::class, 'show'])->name('events.show');
    Route::get('/events/{event:slug}/ics', [EventController::class, 'ics'])->name('events.ics');
    Route::get('/event/{event:slug}/cancel-rsvp/{user}', \App\Http\Controllers\Site\CancelRsvpController::class)
        ->middleware('signed')
        ->name('events.cancel-rsvp.public');

    Route::get('/ministries', [MinistryController::class, 'index'])->name('ministries.index');
    Route::get('/ministries/{ministry:slug}', [MinistryController::class, 'show'])->name('ministries.show');

    Route::get('/news', [BlogController::class, 'index'])->name('blog.index');
    Route::get('/news/{post:slug}', [BlogController::class, 'show'])->name('blog.show');

    Route::get('/qa', [QaController::class, 'index'])->name('qa.index');
});

/*
|--------------------------------------------------------------------------
| Preview routes (design playground)
|--------------------------------------------------------------------------
| Intentionally limited to local/testing environments — they expose
| static seed data and should never be reachable in production. The
| APP_PREVIEW_ENABLED flag offers a manual escape hatch for staging
| review without flipping APP_ENV.
*/
if (app()->environment(['local', 'testing']) || config('app.preview_enabled')) {
    require __DIR__.'/preview.php';
}
