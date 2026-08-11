<?php
use Illuminate\Support\Facades\Route;

Route::prefix('preview')->name('preview.')->group(function () {
    Route::view('/', 'preview.home')->name('home');
    Route::view('/about', 'preview.about')->name('about');
    Route::view('/sermons', 'preview.sermons.index')->name('sermons');
    Route::view('/sermons/sample', 'preview.sermons.show')->name('sermons.show');
    Route::view('/events', 'preview.events.index')->name('events');
    Route::view('/events/sample', 'preview.events.show')->name('events.show');
    Route::view('/ministries', 'preview.ministries.index')->name('ministries');
    Route::view('/ministries/sample', 'preview.ministries.show')->name('ministries.show');
    Route::view('/blog', 'preview.blog.index')->name('blog');
    Route::view('/blog/sample', 'preview.blog.show')->name('blog.show');
    Route::view('/contact', 'preview.contact')->name('contact');
});

Route::prefix('preview/member')->name('preview.member.')->group(function () {
    Route::view('/login',             'preview.member.login')->name('login');
    Route::view('/register',          'preview.member.register')->name('register');
    Route::view('/verify',            'preview.member.verify')->name('verify');
    Route::view('/',                  'preview.member.dashboard')->name('dashboard');
    Route::view('/profile',           'preview.member.profile')->name('profile');
    Route::view('/prayer-requests',   'preview.member.prayer.index')->name('prayer');
    Route::view('/prayer-requests/create', 'preview.member.prayer.create')->name('prayer.create');
    Route::view('/prayer-requests/sample', 'preview.member.prayer.show')->name('prayer.show');
    Route::view('/care',              'preview.member.care.index')->name('care');
    Route::view('/care/create',       'preview.member.care.create')->name('care.create');
    Route::view('/care/thanks',       'preview.member.care.thanks')->name('care.thanks');
    Route::view('/feed',              'preview.member.feed')->name('feed');
    Route::view('/donate',            'preview.member.donate')->name('donate');
});

// Admin design previews sit under /admin/* so they must use the same
// auth gate as the real dashboard — never expose them to guests.
Route::middleware(['auth:admin', 'admin'])
    ->prefix('admin/preview')
    ->name('admin.preview.')
    ->group(function () {
        Route::view('/login', 'preview.admin.login')->name('login');
        Route::view('/', 'preview.admin.dashboard')->name('dashboard');
        Route::view('/pages', 'preview.admin.pages.index')->name('pages');
        Route::view('/pages/edit', 'preview.admin.pages.edit')->name('pages.edit');
        Route::view('/blog', 'preview.admin.blog.index')->name('blog');
        Route::view('/blog/edit', 'preview.admin.blog.edit')->name('blog.edit');
        Route::view('/sermons', 'preview.admin.sermons.index')->name('sermons');
        Route::view('/events', 'preview.admin.events.index')->name('events');
        Route::view('/ministries', 'preview.admin.ministries.index')->name('ministries');
        Route::view('/media', 'preview.admin.media')->name('media');
        Route::view('/menus', 'preview.admin.menus')->name('menus');
        Route::view('/messages', 'preview.admin.messages')->name('messages');
        Route::view('/users', 'preview.admin.users')->name('users');
        Route::view('/roles', 'preview.admin.roles')->name('roles');
        Route::view('/settings', 'preview.admin.settings')->name('settings');
    });
