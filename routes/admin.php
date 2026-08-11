<?php

use App\Http\Controllers\Admin\BlogCategoryController;
use App\Http\Controllers\Admin\BlogPostController;
use App\Http\Controllers\Admin\CertificateController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\MediaFolderController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\MenuItemController;
use App\Http\Controllers\Admin\MessageController;
use App\Http\Controllers\Admin\MinistryController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SermonController;
use App\Http\Controllers\Admin\SermonSeriesController;
use App\Http\Controllers\Admin\SermonSpeakerController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin panel routes (M7)
|--------------------------------------------------------------------------
| Every route here is gated by `auth:admin` + `admin` middleware. Per-
| resource access is checked with the Spatie `permission:*` middleware,
| using the permission names seeded by RolePermissionSeeder.
*/

Route::middleware(['auth:admin', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        Route::resource('pages', PageController::class)
            ->except(['create', 'destroy'])
            ->middleware('permission:manage-pages');

        Route::resource('blog/categories', BlogCategoryController::class)
            ->parameters(['categories' => 'category'])
            ->names('blog.categories')
            ->middleware('permission:manage-blog');
        Route::resource('blog/posts', BlogPostController::class)
            ->parameters(['posts' => 'post'])
            ->names('blog.posts')
            ->middleware('permission:manage-blog');

        Route::resource('sermons/series', SermonSeriesController::class)
            ->parameters(['series' => 'series'])
            ->names('sermons.series')
            ->middleware('permission:manage-sermons');
        Route::resource('sermons/speakers', SermonSpeakerController::class)
            ->parameters(['speakers' => 'speaker'])
            ->names('sermons.speakers')
            ->middleware('permission:manage-sermons');
        Route::resource('sermons', SermonController::class)
            ->middleware('permission:manage-sermons');

        Route::resource('events', EventController::class)
            ->middleware('permission:manage-events');

        Route::post('ministries/reorder', [MinistryController::class, 'reorder'])
            ->name('ministries.reorder')
            ->middleware('permission:manage-ministries');
        Route::resource('ministries', MinistryController::class)
            ->middleware('permission:manage-ministries');

        Route::get('media', [MediaController::class, 'index'])->name('media.index')->middleware('permission:manage-media');
        Route::post('media', [MediaController::class, 'store'])->name('media.store')->middleware('permission:manage-media');
        Route::patch('media/{media}', [MediaController::class, 'update'])->name('media.update')->middleware('permission:manage-media');
        Route::delete('media/{media}', [MediaController::class, 'destroy'])->name('media.destroy')->middleware('permission:manage-media');
        Route::post('media/folders', [MediaFolderController::class, 'store'])->name('media.folders.store')->middleware('permission:manage-media');
        Route::patch('media/folders/{folder}', [MediaFolderController::class, 'update'])->name('media.folders.update')->middleware('permission:manage-media');
        Route::delete('media/folders/{folder}', [MediaFolderController::class, 'destroy'])->name('media.folders.destroy')->middleware('permission:manage-media');

        Route::resource('menus', MenuController::class)
            ->middleware('permission:manage-menus');
        Route::post('menus/{menu}/reorder', [MenuItemController::class, 'reorder'])
            ->name('menus.reorder')
            ->middleware('permission:manage-menus');
        Route::resource('menus.items', MenuItemController::class)
            ->parameters(['items' => 'item'])
            ->shallow()
            ->middleware('permission:manage-menus');

        Route::resource('certificates', CertificateController::class)
            ->middleware('permission:manage-certificates');
        Route::get('certificates/{certificate}/preview', [CertificateController::class, 'preview'])
            ->name('certificates.preview')
            ->middleware('permission:manage-certificates');
        Route::get('certificates/{certificate}/print', [CertificateController::class, 'print'])
            ->name('certificates.print')
            ->middleware('permission:manage-certificates');
        Route::get('certificates/{certificate}/pdf', [CertificateController::class, 'pdf'])
            ->name('certificates.pdf')
            ->middleware('permission:manage-certificates');

        Route::get('messages', [MessageController::class, 'index'])->name('messages.index')->middleware('permission:manage-messages');
        Route::get('messages/{message}', [MessageController::class, 'show'])->name('messages.show')->middleware('permission:manage-messages');
        Route::post('messages/{message}/reply', [MessageController::class, 'reply'])->name('messages.reply')->middleware('permission:manage-messages');
        Route::post('messages/{message}/toggle-public', [MessageController::class, 'togglePublic'])->name('messages.toggle-public')->middleware('permission:manage-messages');
        Route::delete('messages/{message}', [MessageController::class, 'destroy'])->name('messages.destroy')->middleware('permission:manage-messages');

        Route::resource('users', UserController::class)
            ->middleware('permission:manage-users');
        Route::resource('roles', RoleController::class)
            ->middleware('permission:manage-roles');

        Route::get('settings', [SettingsController::class, 'index'])->name('settings.index')->middleware('permission:manage-settings');
        Route::post('settings', [SettingsController::class, 'update'])->name('settings.update')->middleware('permission:manage-settings');

        // M4 Prayer Requests
        Route::resource('prayer-requests', \App\Http\Controllers\Admin\PrayerRequestController::class)
            ->only(['index', 'show', 'update', 'destroy'])
            ->middleware('permission:manage-prayer-requests');

        // M5 Knock for Help
        Route::resource('care', \App\Http\Controllers\Admin\CareController::class)
            ->only(['index', 'show', 'update'])
            ->middleware('permission:manage-knock-help');

        // M6 Community Feed
        Route::resource('feed', \App\Http\Controllers\Admin\FeedController::class)
            ->middleware('permission:manage-community-feed');

        // M5 Attendance
        Route::get('/events/{event}/attendance', [\App\Http\Controllers\Admin\AttendanceController::class, 'show'])
            ->name('events.attendance')
            ->middleware('permission:manage-events');
        Route::post('/events/{event}/checkin-code/regenerate', [\App\Http\Controllers\Admin\AttendanceController::class, 'regenerateCode'])
            ->name('events.checkin-code.regenerate')
            ->middleware('permission:manage-events');
        Route::put('/events/{event}/attendance-open', [\App\Http\Controllers\Admin\AttendanceController::class, 'toggleOpen'])
            ->name('events.attendance.toggle')
            ->middleware('permission:manage-events');
        Route::post('/events/{event}/attendance', [\App\Http\Controllers\Admin\AttendanceController::class, 'store'])
            ->name('events.attendance.store')
            ->middleware('permission:manage-events');
        Route::delete('/events/{event}/attendance/{attendance}', [\App\Http\Controllers\Admin\AttendanceController::class, 'destroy'])
            ->name('events.attendance.destroy')
            ->middleware('permission:manage-events');
        Route::get('/events/{event}/attendance/export', [\App\Http\Controllers\Admin\AttendanceController::class, 'export'])
            ->name('events.attendance.export')
            ->middleware('permission:manage-events');
        Route::get('/attendance', [\App\Http\Controllers\Admin\AttendanceController::class, 'index'])
            ->name('attendance.index')
            ->middleware('permission:manage-events');

        // M6 Tithes (Site Admin only)
        Route::middleware('can:manage-tithes')->prefix('tithes')->name('tithes.')->group(function () {
            Route::resource('funds', \App\Http\Controllers\Admin\TitheFundController::class)->except(['show']);
            Route::get('export', [\App\Http\Controllers\Admin\TitheController::class, 'export'])->name('export');
            Route::get('member-lookup', [\App\Http\Controllers\Admin\MemberLookupController::class, 'search'])->name('member-lookup');
            Route::get('/', [\App\Http\Controllers\Admin\TitheController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Admin\TitheController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Admin\TitheController::class, 'store'])->name('store');
            Route::get('/{tithe}/edit', [\App\Http\Controllers\Admin\TitheController::class, 'edit'])->name('edit');
            Route::put('/{tithe}', [\App\Http\Controllers\Admin\TitheController::class, 'update'])->name('update');
            Route::delete('/{tithe}', [\App\Http\Controllers\Admin\TitheController::class, 'destroy'])->name('destroy');
        });

        // Phase 3 stub modules — sidebar links land here until the real modules ship.
        Route::view('/reminders', 'admin.stubs.phase3', ['module' => 'Reminders'])->name('reminders.stub');

        // M7 Reports
        Route::middleware('can:view-reports')->prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\ReportsController::class, 'index'])->name('index');
            Route::get('/attendance.csv', [\App\Http\Controllers\Admin\ReportsController::class, 'attendanceCsv'])->name('attendance.csv');
            Route::get('/giving.csv',     [\App\Http\Controllers\Admin\ReportsController::class, 'givingCsv'])->name('giving.csv');
        });
    });
