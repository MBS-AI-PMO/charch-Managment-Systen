<?php

use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\MemberLoginController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Member\DashboardController;
use App\Http\Controllers\Member\ProfileController;
use App\Http\Controllers\Member\QuestionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Member auth + portal routes
|--------------------------------------------------------------------------
*/

Route::middleware('guest:web')->group(function () {
    Route::get('/login', [MemberLoginController::class, 'create'])->name('login');
    Route::post('/login', [MemberLoginController::class, 'store'])
        ->middleware('throttle:member-login');

    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store'])
        ->middleware('throttle:member-register');

    Route::get('/forgot', [PasswordResetController::class, 'showLinkRequestMember'])
        ->name('password.request');
    Route::post('/forgot', [PasswordResetController::class, 'sendResetLinkMember'])
        ->middleware('throttle:password-reset')
        ->name('password.email');
    Route::get('/reset/{token}', [PasswordResetController::class, 'showResetMember'])
        ->name('password.reset');
    Route::post('/reset', [PasswordResetController::class, 'resetMember'])
        ->name('password.update');
});

Route::middleware('auth:web')->group(function () {
    Route::post('/logout', [MemberLoginController::class, 'destroy'])->name('logout');

    // Email verification — accessible to authenticated but unverified users.
    Route::get('/email/verify', [EmailVerificationController::class, 'notice'])
        ->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');
    Route::post('/email/verification-notification', [EmailVerificationController::class, 'send'])
        ->middleware('throttle:6,1')
        ->name('verification.send');
});

Route::middleware(['auth:web', 'verified'])->prefix('member')->name('member.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    Route::get('/questions', [QuestionController::class, 'index'])->name('questions.index');
    Route::get('/questions/{question}', [QuestionController::class, 'show'])->name('questions.show');

    // M4 Prayer Requests
    Route::post('/prayer-requests', [\App\Http\Controllers\Member\PrayerRequestController::class, 'store'])
        ->middleware('throttle:prayer-submit')
        ->name('prayer.store');
    Route::resource('prayer-requests', \App\Http\Controllers\Member\PrayerRequestController::class)
        ->only(['index', 'create', 'show', 'destroy'])
        ->parameters(['prayer-requests' => 'prayer'])
        ->names('prayer');
    Route::post('/prayer-requests/{prayer}/pray', [\App\Http\Controllers\Member\PrayerRequestController::class, 'togglePray'])
        ->name('prayer.pray')
        ->middleware('throttle:feed-react');

    // M5 Knock for Help
    // Throttled POST store route declared standalone (base Controller has no middleware() method in Laravel 12).
    Route::post('/care', [\App\Http\Controllers\Member\CareRequestController::class, 'store'])
        ->middleware('throttle:care-submit')
        ->name('care.store');

    Route::view('/care/thanks', 'member.care.thanks')->name('care.thanks');

    Route::resource('care', \App\Http\Controllers\Member\CareRequestController::class)
        ->only(['index', 'create', 'show']);

    // M6 Community Feed
    Route::get('/feed', [\App\Http\Controllers\Member\FeedController::class, 'index'])->name('feed.index');
    Route::post('/feed/{post}/react', [\App\Http\Controllers\Member\FeedController::class, 'react'])
        ->middleware('throttle:feed-react')
        ->name('feed.react');

    // M7 Giving — redirects to the public donate page.
    Route::get('/giving', fn () => redirect()->route('site.donate'))->name('giving');

    // M2 Event RSVPs
    Route::post('/events/{event:slug}/rsvp', [\App\Http\Controllers\Member\EventController::class, 'rsvp'])
        ->name('events.rsvp')->middleware('throttle:event-rsvp');
    Route::delete('/events/{event:slug}/rsvp', [\App\Http\Controllers\Member\EventController::class, 'cancelRsvp'])
        ->name('events.cancel-rsvp');

    // M3 Check-in
    Route::get('/check-in',  [\App\Http\Controllers\Member\CheckinController::class, 'show'])->name('checkin.show');
    Route::post('/check-in', [\App\Http\Controllers\Member\CheckinController::class, 'submit'])
        ->middleware('throttle:checkin-submit')
        ->name('checkin.submit');
    Route::get('/check-in/thanks/{attendance}', [\App\Http\Controllers\Member\CheckinController::class, 'thanks'])
        ->name('checkin.thanks');
});
