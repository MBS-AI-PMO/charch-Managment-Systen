# Church CMS Phase 1 Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Ship Phase 1 of the Church CMS — public website (7 dynamically-editable pages) + admin panel with role-based auth + WYSIWYG content management + media library + site-wide settings.

**Architecture:** Laravel 12 monolith with Blade templates. Two auth guards (`admin`, `web`) on a single `users` table. Custom hand-rolled auth (no Breeze/Jetstream). Spatie Laravel-Permission for roles. Tailwind CSS + Alpine.js on the front-end. UI/UX built first under `/preview` and `/admin/preview` route groups with static seed data for sign-off, then wired to controllers + DB.

**Tech Stack:** PHP 8.3+, Laravel 12, MySQL 8 (XAMPP), Tailwind v3, Alpine.js, Vite, Spatie Laravel-Permission, CKEditor 5, mews/purifier, Intervention Image, Pest.

**No git in this plan** — per user instruction, no commit steps. Re-introduce when ready.

---

## Reading order

Tasks are grouped into **Milestones** (M1–M12). Each milestone produces a working slice; between M3↔M4 and M4↔M5 there are explicit user-sign-off gates because of the design-first workflow.

| Milestone | What ships |
|---|---|
| M1 | Bootstrap, Tailwind/Vite, Google Fonts, base layouts |
| M2 | Migrations + Spatie + Models + Seeders |
| M3 | Public site UI under `/preview/*` (static) — **SIGN-OFF GATE** |
| M4 | Admin UI under `/admin/preview/*` (static) — **SIGN-OFF GATE** |
| M5 | Real auth (admin + member) |
| M6 | Public site wired to DB |
| M7 | Admin CRUD wired to DB |
| M8 | Media library + CKEditor 5 |
| M9 | Contact form + mail |
| M10 | Security hardening |
| M11 | Tests |
| M12 | Polish, README, deployment notes |

---

# Milestone 1 — Bootstrap

### Task M1-T01: Create Laravel 12 project

**Files:**
- Create: `composer.json` (Laravel default)
- Create: project tree

- [ ] **Step 1:** Open a PowerShell at the parent of the project folder. Run:
  ```powershell
  composer create-project laravel/laravel "Chruch Managment System" "^12.0" --prefer-dist
  ```
- [ ] **Step 2:** Confirm `php artisan --version` returns `Laravel Framework 12.x.y` from within the project folder.
- [ ] **Step 3:** Verify `php artisan serve` starts on `http://127.0.0.1:8000` and shows the Laravel welcome page.

### Task M1-T02: Environment configuration

**Files:**
- Modify: `.env`
- Modify: `.env.example`

- [ ] **Step 1:** Update `.env` with project values:
  ```env
  APP_NAME="Church CMS"
  APP_URL=http://localhost/church
  APP_LOCALE=en
  APP_TIMEZONE=UTC

  DB_CONNECTION=mysql
  DB_HOST=127.0.0.1
  DB_PORT=3306
  DB_DATABASE=church_cms
  DB_USERNAME=root
  DB_PASSWORD=

  SESSION_DRIVER=database
  SESSION_LIFETIME=120
  SESSION_SECURE_COOKIE=false
  SESSION_SAME_SITE=lax

  MAIL_MAILER=log
  MAIL_FROM_ADDRESS="hello@church.local"
  MAIL_FROM_NAME="${APP_NAME}"

  # Bootstrap admin (Site Admin seeder reads these)
  ADMIN_NAME="Site Admin"
  ADMIN_EMAIL="admin@church.local"
  ADMIN_PASSWORD="ChangeMe!2026"

  RECAPTCHA_SITE_KEY=
  RECAPTCHA_SECRET_KEY=
  ```
- [ ] **Step 2:** Mirror keys (without secrets) into `.env.example`. Replace every secret value with the placeholder `"changeme"` or empty string.
- [ ] **Step 3:** Create MySQL database `church_cms` via phpMyAdmin or `mysql -uroot -e "CREATE DATABASE church_cms CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"`.
- [ ] **Step 4:** Run `php artisan migrate` to confirm DB connection (will fail on missing tables — that's fine, just confirm no connection error).
- [ ] **Step 5:** Run `php artisan key:generate`.

### Task M1-T03: Install backend packages

**Files:**
- Modify: `composer.json`

- [ ] **Step 1:** Install packages:
  ```powershell
  composer require spatie/laravel-permission mews/purifier intervention/image-laravel
  composer require --dev pestphp/pest pestphp/pest-plugin-laravel laravel/dusk
  ```
- [ ] **Step 2:** Publish Spatie config:
  ```powershell
  php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
  ```
- [ ] **Step 3:** Publish HTMLPurifier config:
  ```powershell
  php artisan vendor:publish --provider="Mews\Purifier\PurifierServiceProvider"
  ```
- [ ] **Step 4:** Initialize Pest:
  ```powershell
  ./vendor/bin/pest --init
  ```
- [ ] **Step 5:** Run `./vendor/bin/pest` to confirm test runner works (the default `ExampleTest` will pass).

### Task M1-T04: Tailwind + Vite + Alpine + Google Fonts

**Files:**
- Modify: `package.json`
- Create: `tailwind.config.js`
- Create: `postcss.config.js`
- Modify: `resources/css/app.css`
- Modify: `resources/js/app.js`
- Modify: `vite.config.js`
- Modify: `resources/views/layouts/site.blade.php`
- Modify: `resources/views/layouts/admin.blade.php`

- [ ] **Step 1:** Install:
  ```powershell
  npm install -D tailwindcss@^3 postcss autoprefixer @tailwindcss/forms @tailwindcss/typography
  npm install alpinejs
  npx tailwindcss init -p
  ```
- [ ] **Step 2:** Replace `tailwind.config.js`:
  ```js
  /** @type {import('tailwindcss').Config} */
  export default {
    content: [
      './resources/**/*.blade.php',
      './resources/**/*.js',
      './app/View/Components/**/*.php',
    ],
    theme: {
      container: { center: true, padding: '1rem' },
      extend: {
        colors: {
          brand: {
            primary: 'rgb(var(--brand-primary) / <alpha-value>)',
            secondary: 'rgb(var(--brand-secondary) / <alpha-value>)',
          },
          surface: 'rgb(var(--surface) / <alpha-value>)',
          'surface-elevated': 'rgb(var(--surface-elevated) / <alpha-value>)',
          ink: 'rgb(var(--ink) / <alpha-value>)',
          'ink-muted': 'rgb(var(--ink-muted) / <alpha-value>)',
        },
        fontFamily: {
          serif: ['"Fraunces"', 'Georgia', 'serif'],
          sans: ['"Inter"', 'system-ui', 'sans-serif'],
        },
        maxWidth: { container: '1200px' },
      },
    },
    plugins: [require('@tailwindcss/forms'), require('@tailwindcss/typography')],
  };
  ```
- [ ] **Step 3:** Replace `resources/css/app.css`:
  ```css
  @tailwind base;
  @tailwind components;
  @tailwind utilities;

  @layer base {
    :root {
      --brand-primary: 122 31 43;
      --brand-secondary: 201 169 97;
      --surface: 250 247 242;
      --surface-elevated: 255 255 255;
      --ink: 44 40 37;
      --ink-muted: 107 100 94;
      --border: 232 226 216;
    }
    body { @apply bg-surface text-ink font-sans antialiased; }
    h1, h2, h3, h4 { @apply font-serif; }
  }

  @layer components {
    .btn { @apply inline-flex items-center justify-center rounded-lg px-5 py-2.5 font-medium transition; }
    .btn-primary { @apply btn bg-brand-primary text-white hover:opacity-90; }
    .btn-secondary { @apply btn bg-brand-secondary text-ink hover:opacity-90; }
    .btn-ghost { @apply btn border border-[rgb(var(--border))] hover:bg-white; }
    .card { @apply bg-surface-elevated border border-[rgb(var(--border))] rounded-xl shadow-sm; }
    .input { @apply w-full rounded-md border-[rgb(var(--border))] focus:border-brand-primary focus:ring-brand-primary; }
  }
  ```
- [ ] **Step 4:** Replace `resources/js/app.js`:
  ```js
  import Alpine from 'alpinejs';
  window.Alpine = Alpine;
  Alpine.start();
  ```
- [ ] **Step 5:** Create `resources/views/layouts/site.blade.php`:
  ```blade
  <!DOCTYPE html>
  <html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('app.name'))</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,600&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
  </head>
  <body>
    {{ $slot ?? '' }}
    @yield('content')
    @stack('scripts')
  </body>
  </html>
  ```
- [ ] **Step 6:** Create `resources/views/layouts/admin.blade.php` (same structure as `site.blade.php` but with admin-styled body classes — `class="bg-[#F7F5F1]"`).
- [ ] **Step 7:** Run `npm run dev` in one terminal and `php artisan serve` in another. Visit `http://127.0.0.1:8000` — base layout should load with Fraunces/Inter fonts.

---

# Milestone 2 — Database & Models

### Task M2-T01: Add `is_admin` column to users + Spatie migration

**Files:**
- Modify: existing `database/migrations/0001_01_01_000000_create_users_table.php`
- Run: `database/migrations/<timestamp>_create_permission_tables.php` (auto-generated by Spatie publish)

- [ ] **Step 1:** Open the default users-table migration and add inside the `Schema::create('users', …)` callback, after the email column:
  ```php
  $table->boolean('is_admin')->default(false)->index();
  $table->string('avatar_path')->nullable();
  $table->boolean('two_factor_enabled')->default(false);
  ```
- [ ] **Step 2:** Run `php artisan migrate` (creates users + Spatie tables together).
- [ ] **Step 3:** Verify in MySQL that `users.is_admin` exists and Spatie's `roles`, `permissions`, `model_has_*` tables are present.

### Task M2-T02: Site settings + menus migrations

**Files:**
- Create: `database/migrations/<ts>_create_site_settings_table.php`
- Create: `database/migrations/<ts>_create_menus_tables.php`

- [ ] **Step 1:** Generate: `php artisan make:migration create_site_settings_table`. Replace `up()`:
  ```php
  Schema::create('site_settings', function (Blueprint $table) {
      $table->string('key')->primary();
      $table->longText('value')->nullable();
      $table->enum('type', ['text','image','json','color','html'])->default('text');
      $table->string('group')->default('general')->index();
      $table->timestamps();
  });
  ```
- [ ] **Step 2:** Generate: `php artisan make:migration create_menus_tables`. Replace `up()`:
  ```php
  Schema::create('menus', function (Blueprint $t) {
      $t->id();
      $t->string('slug')->unique();
      $t->string('name');
      $t->timestamps();
  });
  Schema::create('menu_items', function (Blueprint $t) {
      $t->id();
      $t->foreignId('menu_id')->constrained()->cascadeOnDelete();
      $t->foreignId('parent_id')->nullable()->constrained('menu_items')->nullOnDelete();
      $t->string('label');
      $t->enum('link_type', ['page','url','route'])->default('url');
      $t->string('link_value');
      $t->enum('target', ['_self','_blank'])->default('_self');
      $t->unsignedInteger('sort_order')->default(0);
      $t->timestamps();
      $t->index(['menu_id','parent_id','sort_order']);
  });
  ```
- [ ] **Step 3:** Provide matching `down()` methods that drop both tables.
- [ ] **Step 4:** Run `php artisan migrate`.

### Task M2-T03: CMS page migrations

**Files:**
- Create: `database/migrations/<ts>_create_pages_table.php`
- Create: `database/migrations/<ts>_create_page_sections_table.php`
- Create: `database/migrations/<ts>_create_page_revisions_table.php`

- [ ] **Step 1:** Create `pages` migration:
  ```php
  Schema::create('pages', function (Blueprint $t) {
      $t->id();
      $t->string('slug')->unique();
      $t->string('title');
      $t->string('hero_heading')->nullable();
      $t->string('hero_subheading')->nullable();
      $t->string('hero_image_path')->nullable();
      $t->longText('body')->nullable();
      $t->string('meta_title')->nullable();
      $t->string('meta_description', 500)->nullable();
      $t->string('meta_og_image_path')->nullable();
      $t->boolean('is_published')->default(false);
      $t->timestamp('published_at')->nullable();
      $t->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
      $t->timestamps();
      $t->index(['is_published','published_at']);
  });
  ```
- [ ] **Step 2:** `page_sections`:
  ```php
  Schema::create('page_sections', function (Blueprint $t) {
      $t->id();
      $t->foreignId('page_id')->constrained()->cascadeOnDelete();
      $t->string('type');                 // rich-text, image-grid, cta, stats, testimonials
      $t->json('payload')->nullable();
      $t->unsignedInteger('sort_order')->default(0);
      $t->timestamps();
      $t->index(['page_id','sort_order']);
  });
  ```
- [ ] **Step 3:** `page_revisions`:
  ```php
  Schema::create('page_revisions', function (Blueprint $t) {
      $t->id();
      $t->foreignId('page_id')->constrained()->cascadeOnDelete();
      $t->longText('snapshot');           // JSON-encoded full page state
      $t->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
      $t->timestamp('created_at')->useCurrent();
      $t->index(['page_id','created_at']);
  });
  ```
- [ ] **Step 4:** `php artisan migrate`.

### Task M2-T04: Blog migrations

**Files:**
- Create: `database/migrations/<ts>_create_blog_tables.php`

- [ ] **Step 1:** One migration creating both tables in order:
  ```php
  Schema::create('blog_categories', function (Blueprint $t) {
      $t->id();
      $t->string('slug')->unique();
      $t->string('name');
      $t->timestamps();
  });
  Schema::create('blog_posts', function (Blueprint $t) {
      $t->id();
      $t->string('slug')->unique();
      $t->string('title');
      $t->string('excerpt', 500)->nullable();
      $t->longText('body');
      $t->string('featured_image_path')->nullable();
      $t->foreignId('category_id')->nullable()->constrained('blog_categories')->nullOnDelete();
      $t->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
      $t->string('meta_title')->nullable();
      $t->string('meta_description', 500)->nullable();
      $t->boolean('is_published')->default(false);
      $t->timestamp('published_at')->nullable();
      $t->timestamps();
      $t->index(['is_published','published_at']);
  });
  ```
- [ ] **Step 2:** Add matching `down()`. Run migrate.

### Task M2-T05: Sermon migrations

**Files:**
- Create: `database/migrations/<ts>_create_sermon_tables.php`

- [ ] **Step 1:**
  ```php
  Schema::create('sermon_series', function (Blueprint $t) {
      $t->id();
      $t->string('slug')->unique();
      $t->string('name');
      $t->text('description')->nullable();
      $t->string('cover_image_path')->nullable();
      $t->timestamps();
  });
  Schema::create('sermon_speakers', function (Blueprint $t) {
      $t->id();
      $t->string('slug')->unique();
      $t->string('name');
      $t->string('role')->nullable();
      $t->text('bio')->nullable();
      $t->string('photo_path')->nullable();
      $t->timestamps();
  });
  Schema::create('sermons', function (Blueprint $t) {
      $t->id();
      $t->string('slug')->unique();
      $t->string('title');
      $t->text('summary')->nullable();
      $t->longText('body')->nullable();
      $t->foreignId('series_id')->nullable()->constrained('sermon_series')->nullOnDelete();
      $t->foreignId('speaker_id')->nullable()->constrained('sermon_speakers')->nullOnDelete();
      $t->string('scripture_reference')->nullable();
      $t->date('preached_on')->nullable();
      $t->string('audio_url')->nullable();
      $t->string('video_url')->nullable();
      $t->string('thumbnail_path')->nullable();
      $t->boolean('downloads_enabled')->default(true);
      $t->boolean('is_published')->default(false);
      $t->timestamps();
      $t->index(['is_published','preached_on']);
  });
  ```
- [ ] **Step 2:** Run migrate.

### Task M2-T06: Events + Ministries + Media + Contact + Activity migrations

**Files:**
- Create: `database/migrations/<ts>_create_events_table.php`
- Create: `database/migrations/<ts>_create_ministries_table.php`
- Create: `database/migrations/<ts>_create_media_tables.php`
- Create: `database/migrations/<ts>_create_contact_messages_table.php`
- Create: `database/migrations/<ts>_create_activity_log_table.php`

- [ ] **Step 1:** Events:
  ```php
  Schema::create('events', function (Blueprint $t) {
      $t->id();
      $t->string('slug')->unique();
      $t->string('title');
      $t->longText('description')->nullable();
      $t->string('location')->nullable();
      $t->dateTime('starts_at');
      $t->dateTime('ends_at')->nullable();
      $t->string('cover_image_path')->nullable();
      $t->string('registration_url')->nullable();
      $t->boolean('is_published')->default(false);
      $t->boolean('is_featured')->default(false);
      $t->timestamps();
      $t->index(['is_published','starts_at']);
  });
  ```
- [ ] **Step 2:** Ministries:
  ```php
  Schema::create('ministries', function (Blueprint $t) {
      $t->id();
      $t->string('slug')->unique();
      $t->string('name');
      $t->string('summary', 500)->nullable();
      $t->longText('body')->nullable();
      $t->string('leader_name')->nullable();
      $t->string('contact_email')->nullable();
      $t->string('cover_image_path')->nullable();
      $t->unsignedInteger('sort_order')->default(0);
      $t->boolean('is_published')->default(false);
      $t->timestamps();
      $t->index(['is_published','sort_order']);
  });
  ```
- [ ] **Step 3:** Media:
  ```php
  Schema::create('media_folders', function (Blueprint $t) {
      $t->id();
      $t->foreignId('parent_id')->nullable()->constrained('media_folders')->nullOnDelete();
      $t->string('name');
      $t->string('slug');
      $t->string('path');
      $t->timestamps();
      $t->unique(['parent_id','slug']);
  });
  Schema::create('media', function (Blueprint $t) {
      $t->id();
      $t->foreignId('folder_id')->nullable()->constrained('media_folders')->nullOnDelete();
      $t->string('disk')->default('public');
      $t->string('path')->unique();
      $t->string('filename');
      $t->string('mime_type');
      $t->unsignedBigInteger('size');
      $t->unsignedInteger('width')->nullable();
      $t->unsignedInteger('height')->nullable();
      $t->string('alt_text')->nullable();
      $t->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
      $t->timestamps();
  });
  ```
- [ ] **Step 4:** Contact:
  ```php
  Schema::create('contact_messages', function (Blueprint $t) {
      $t->id();
      $t->string('name');
      $t->string('email');
      $t->string('phone')->nullable();
      $t->string('subject');
      $t->text('message');
      $t->string('ip', 45)->nullable();
      $t->string('user_agent')->nullable();
      $t->timestamp('read_at')->nullable();
      $t->timestamps();
      $t->index('read_at');
  });
  ```
- [ ] **Step 5:** Activity log:
  ```php
  Schema::create('activity_log', function (Blueprint $t) {
      $t->id();
      $t->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
      $t->string('subject_type');
      $t->unsignedBigInteger('subject_id');
      $t->string('action');
      $t->json('changes')->nullable();
      $t->string('ip', 45)->nullable();
      $t->timestamp('created_at')->useCurrent();
      $t->index(['subject_type','subject_id']);
  });
  ```
- [ ] **Step 6:** Run all migrations: `php artisan migrate`.

### Task M2-T07: User model + HasRoles

**Files:**
- Modify: `app/Models/User.php`

- [ ] **Step 1:** Edit to add trait and fillables:
  ```php
  use Illuminate\Foundation\Auth\User as Authenticatable;
  use Spatie\Permission\Traits\HasRoles;

  class User extends Authenticatable {
      use HasRoles, Notifiable;

      protected $fillable = [
          'name','email','password','is_admin','avatar_path','two_factor_enabled',
      ];

      protected $hidden = ['password','remember_token','two_factor_secret'];

      protected function casts(): array {
          return [
              'email_verified_at' => 'datetime',
              'password' => 'hashed',
              'is_admin' => 'boolean',
              'two_factor_enabled' => 'boolean',
          ];
      }
  }
  ```

### Task M2-T08: Slug + LogsModelActivity traits

**Files:**
- Create: `app/Support/Sluggable.php`
- Create: `app/Support/LogsModelActivity.php`

- [ ] **Step 1:** Create `app/Support/Sluggable.php`:
  ```php
  <?php
  namespace App\Support;
  use Illuminate\Support\Str;

  trait Sluggable {
      protected static function bootSluggable(): void {
          static::saving(function ($model) {
              if (empty($model->slug) && !empty($model->{$model->getSlugSource()})) {
                  $base = Str::slug($model->{$model->getSlugSource()});
                  $slug = $base; $i = 2;
                  while (static::where('slug', $slug)->where('id', '!=', $model->id)->exists()) {
                      $slug = $base.'-'.$i++;
                  }
                  $model->slug = $slug;
              }
          });
      }
      public function getSlugSource(): string { return property_exists($this, 'slugSource') ? $this->slugSource : 'title'; }
  }
  ```
- [ ] **Step 2:** Create `app/Support/LogsModelActivity.php`:
  ```php
  <?php
  namespace App\Support;
  use App\Models\ActivityLog;

  trait LogsModelActivity {
      protected static function bootLogsModelActivity(): void {
          foreach (['created','updated','deleted'] as $event) {
              static::$event(function ($model) use ($event) {
                  ActivityLog::create([
                      'user_id' => auth()->id(),
                      'subject_type' => static::class,
                      'subject_id' => $model->id ?? 0,
                      'action' => $event,
                      'changes' => $event === 'updated' ? $model->getChanges() : null,
                      'ip' => request()?->ip(),
                  ]);
              });
          }
      }
  }
  ```

### Task M2-T09: Models for every table

**Files:**
- Create: `app/Models/Page.php`, `PageSection.php`, `PageRevision.php`
- Create: `app/Models/BlogCategory.php`, `BlogPost.php`
- Create: `app/Models/SermonSeries.php`, `SermonSpeaker.php`, `Sermon.php`
- Create: `app/Models/Event.php`
- Create: `app/Models/Ministry.php`
- Create: `app/Models/MediaFolder.php`, `Media.php`
- Create: `app/Models/Menu.php`, `MenuItem.php`
- Create: `app/Models/SiteSetting.php`
- Create: `app/Models/ContactMessage.php`
- Create: `app/Models/ActivityLog.php`

- [ ] **Step 1:** Use a template — example for `Page.php`:
  ```php
  <?php
  namespace App\Models;
  use App\Support\{Sluggable, LogsModelActivity};
  use Illuminate\Database\Eloquent\Model;

  class Page extends Model {
      use Sluggable, LogsModelActivity;
      protected $guarded = [];
      protected $casts = ['is_published'=>'bool','published_at'=>'datetime'];
      public function sections() { return $this->hasMany(PageSection::class)->orderBy('sort_order'); }
      public function revisions() { return $this->hasMany(PageRevision::class)->latest(); }
      public function scopePublished($q) { return $q->where('is_published',true)->whereNotNull('published_at')->where('published_at','<=',now()); }
  }
  ```
- [ ] **Step 2:** Apply the same shape to each model. Relationships to add:
  - `BlogPost`: `belongsTo(BlogCategory::class, 'category_id')`, `belongsTo(User::class,'author_id')`
  - `Sermon`: `belongsTo(SermonSeries::class,'series_id')`, `belongsTo(SermonSpeaker::class,'speaker_id')`
  - `MenuItem`: `belongsTo(Menu::class)`, `belongsTo(MenuItem::class,'parent_id')`, `hasMany(MenuItem::class,'parent_id')`
  - `Media`: `belongsTo(MediaFolder::class,'folder_id')`, `belongsTo(User::class,'uploaded_by')`
- [ ] **Step 3:** `SiteSetting`: primary key is `key`, disable incrementing & timestamps as needed:
  ```php
  class SiteSetting extends Model {
      protected $primaryKey = 'key';
      protected $keyType = 'string';
      public $incrementing = false;
      protected $guarded = [];
  }
  ```
- [ ] **Step 4:** `ActivityLog`: `$guarded = []`, `$casts = ['changes'=>'array']`, `public $timestamps = false`.

### Task M2-T10: Roles & permissions seeder

**Files:**
- Create: `database/seeders/RolePermissionSeeder.php`

- [ ] **Step 1:**
  ```php
  <?php
  namespace Database\Seeders;
  use Illuminate\Database\Seeder;
  use Spatie\Permission\Models\{Role, Permission};

  class RolePermissionSeeder extends Seeder {
      public function run(): void {
          $perms = [
              'manage-pages','manage-blog','manage-sermons','manage-events','manage-ministries',
              'manage-media','manage-menus','manage-settings','manage-users','manage-roles',
              'manage-messages','view-reports',
          ];
          foreach ($perms as $p) Permission::firstOrCreate(['name'=>$p,'guard_name'=>'admin']);
          $siteAdmin = Role::firstOrCreate(['name'=>'Site Admin','guard_name'=>'admin']);
          $siteAdmin->syncPermissions($perms);
          Role::firstOrCreate(['name'=>'Event Organizer','guard_name'=>'admin']);
          Role::firstOrCreate(['name'=>'Prayer Organizer','guard_name'=>'admin']);
      }
  }
  ```

### Task M2-T11: Site admin bootstrap seeder

**Files:**
- Create: `database/seeders/SiteAdminSeeder.php`

- [ ] **Step 1:**
  ```php
  <?php
  namespace Database\Seeders;
  use App\Models\User;
  use Illuminate\Database\Seeder;

  class SiteAdminSeeder extends Seeder {
      public function run(): void {
          $u = User::updateOrCreate(
              ['email'=>env('ADMIN_EMAIL','admin@church.local')],
              [
                  'name'=>env('ADMIN_NAME','Site Admin'),
                  'password'=>env('ADMIN_PASSWORD','ChangeMe!2026'),
                  'is_admin'=>true,
                  'email_verified_at'=>now(),
              ]
          );
          $u->syncRoles(['Site Admin']);
      }
  }
  ```

### Task M2-T12: Site settings seeder

**Files:**
- Create: `database/seeders/SiteSettingsSeeder.php`

- [ ] **Step 1:**
  ```php
  <?php
  namespace Database\Seeders;
  use App\Models\SiteSetting;
  use Illuminate\Database\Seeder;

  class SiteSettingsSeeder extends Seeder {
      public function run(): void {
          $defaults = [
              ['key'=>'brand.name','value'=>'Grace Community Church','type'=>'text','group'=>'brand'],
              ['key'=>'brand.tagline','value'=>'A place to belong','type'=>'text','group'=>'brand'],
              ['key'=>'brand.logo','value'=>null,'type'=>'image','group'=>'brand'],
              ['key'=>'brand.favicon','value'=>null,'type'=>'image','group'=>'brand'],
              ['key'=>'brand.color.primary','value'=>'#7A1F2B','type'=>'color','group'=>'brand'],
              ['key'=>'brand.color.secondary','value'=>'#C9A961','type'=>'color','group'=>'brand'],
              ['key'=>'contact.address','value'=>'123 Faith St, Springfield, USA','type'=>'text','group'=>'contact'],
              ['key'=>'contact.phone','value'=>'(555) 123-4567','type'=>'text','group'=>'contact'],
              ['key'=>'contact.email','value'=>'hello@church.local','type'=>'text','group'=>'contact'],
              ['key'=>'contact.service_times','value'=>"Sunday 9:00 AM & 11:00 AM\nWednesday 7:00 PM",'type'=>'text','group'=>'contact'],
              ['key'=>'social.facebook','value'=>'','type'=>'text','group'=>'social'],
              ['key'=>'social.instagram','value'=>'','type'=>'text','group'=>'social'],
              ['key'=>'social.youtube','value'=>'','type'=>'text','group'=>'social'],
              ['key'=>'social.x','value'=>'','type'=>'text','group'=>'social'],
              ['key'=>'footer.about','value'=>'A welcoming community for everyone.','type'=>'text','group'=>'footer'],
              ['key'=>'seo.default_title_suffix','value'=>' | Grace Community Church','type'=>'text','group'=>'seo'],
              ['key'=>'seo.default_description','value'=>'Welcome to Grace Community Church.','type'=>'text','group'=>'seo'],
              ['key'=>'seo.analytics_script','value'=>'','type'=>'html','group'=>'seo'],
          ];
          foreach ($defaults as $row) SiteSetting::updateOrCreate(['key'=>$row['key']], $row);
      }
  }
  ```

### Task M2-T13: Menus seeder

**Files:**
- Create: `database/seeders/MenusSeeder.php`

- [ ] **Step 1:**
  ```php
  <?php
  namespace Database\Seeders;
  use App\Models\{Menu, MenuItem};
  use Illuminate\Database\Seeder;

  class MenusSeeder extends Seeder {
      public function run(): void {
          $main = Menu::updateOrCreate(['slug'=>'main'],['name'=>'Main Navigation']);
          $items = [
              ['label'=>'Home','link_type'=>'route','link_value'=>'site.home'],
              ['label'=>'About','link_type'=>'route','link_value'=>'site.about'],
              ['label'=>'Sermons','link_type'=>'route','link_value'=>'site.sermons.index'],
              ['label'=>'Events','link_type'=>'route','link_value'=>'site.events.index'],
              ['label'=>'Ministries','link_type'=>'route','link_value'=>'site.ministries.index'],
              ['label'=>'Blog','link_type'=>'route','link_value'=>'site.blog.index'],
              ['label'=>'Contact','link_type'=>'route','link_value'=>'site.contact'],
          ];
          foreach ($items as $i => $row) {
              MenuItem::updateOrCreate(
                  ['menu_id'=>$main->id,'label'=>$row['label']],
                  $row + ['sort_order'=>$i]
              );
          }
          Menu::updateOrCreate(['slug'=>'footer'],['name'=>'Footer Quick Links']);
      }
  }
  ```

### Task M2-T14: Demo content seeder (local only)

**Files:**
- Create: `database/seeders/DemoContentSeeder.php`

- [ ] **Step 1:** Populate `pages` (home, about, contact + index pages for sermons/events/ministries/blog), 3 blog categories, 6 blog posts, 2 sermon series, 3 speakers, 8 sermons, 5 events (3 future, 2 past), 6 ministries. Use Eloquent `::create()` with realistic content (50-150 words per body, sample images via `public/images/seed/*` — engineer can drop in placeholders).
- [ ] **Step 2:** Each `create()` call must include `is_published=true` and `published_at=now()` so the public site has visible content.

### Task M2-T15: DatabaseSeeder wires everything

**Files:**
- Modify: `database/seeders/DatabaseSeeder.php`

- [ ] **Step 1:**
  ```php
  public function run(): void {
      $this->call([
          RolePermissionSeeder::class,
          SiteAdminSeeder::class,
          SiteSettingsSeeder::class,
          MenusSeeder::class,
      ]);
      if (app()->environment('local')) $this->call(DemoContentSeeder::class);
  }
  ```
- [ ] **Step 2:** Run: `php artisan migrate:fresh --seed`. Confirm `users` has the bootstrap admin, Spatie tables populated, `pages`/`blog_posts`/`sermons`/`events`/`ministries` all populated.

---

# Milestone 3 — Public Site UI/UX (static `/preview/*`) — SIGN-OFF GATE

> **Goal of M3:** A clickable static prototype of the entire public website using hard-coded data inside Blade templates. No DB queries yet. The user signs off the look & feel before any DB wiring.

### Task M3-T01: Public route group + seed data helper

**Files:**
- Create: `routes/preview.php`
- Modify: `routes/web.php` (require the new file)
- Create: `resources/views/preview/_seed.blade.php`

- [ ] **Step 1:** Append to `routes/web.php`:
  ```php
  if (app()->environment(['local','testing']) || config('app.preview_enabled')) {
      require __DIR__.'/preview.php';
  }
  ```
- [ ] **Step 2:** Create `routes/preview.php`:
  ```php
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
  ```
- [ ] **Step 3:** Create `resources/views/preview/_seed.blade.php` with PHP arrays representing sample sermons/events/blog/ministries (3-6 items each). Use `@include('preview._seed')` at the top of every preview view to make `$sermons`, `$events`, `$ministries`, `$posts` arrays available.

### Task M3-T02: Site Blade components (header, footer, hero, card, etc.)

**Files:**
- Create: `resources/views/components/site/header.blade.php`
- Create: `resources/views/components/site/footer.blade.php`
- Create: `resources/views/components/site/hero.blade.php`
- Create: `resources/views/components/site/section-heading.blade.php`
- Create: `resources/views/components/site/card.blade.php`
- Create: `resources/views/components/site/cta-band.blade.php`
- Create: `resources/views/components/site/breadcrumb.blade.php`

- [ ] **Step 1:** Header (sticky, responsive, Alpine off-canvas drawer for mobile):
  ```blade
  @props(['nav' => []])
  <header x-data="{open:false}" class="sticky top-0 z-40 bg-surface/95 backdrop-blur border-b border-[rgb(var(--border))]">
    <div class="max-w-container mx-auto flex items-center justify-between px-4 py-4">
      <a href="/" class="font-serif text-2xl font-semibold">Grace Community</a>
      <nav class="hidden md:flex items-center gap-7">
        @foreach($nav as $item)
          <a href="{{ $item['url'] }}" class="text-ink hover:text-brand-primary text-[15px]">{{ $item['label'] }}</a>
        @endforeach
        <a href="#" class="btn-primary">Plan your visit</a>
      </nav>
      <button @click="open=true" class="md:hidden p-2" aria-label="Menu"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg></button>
    </div>
    <div x-show="open" x-cloak class="fixed inset-0 z-50 md:hidden">
      <div class="absolute inset-0 bg-black/50" @click="open=false"></div>
      <div class="absolute right-0 top-0 h-full w-72 bg-surface p-6 flex flex-col gap-4">
        <button @click="open=false" class="self-end p-2" aria-label="Close">&times;</button>
        @foreach($nav as $item)
          <a href="{{ $item['url'] }}" class="text-lg py-2 border-b border-[rgb(var(--border))]">{{ $item['label'] }}</a>
        @endforeach
        <a href="#" class="btn-primary mt-4">Plan your visit</a>
      </div>
    </div>
  </header>
  ```
- [ ] **Step 2:** Footer (4-col):
  ```blade
  @props(['address'=>'','phone'=>'','email'=>'','services'=>''])
  <footer class="bg-ink text-white mt-24">
    <div class="max-w-container mx-auto px-4 py-16 grid grid-cols-1 md:grid-cols-4 gap-10">
      <div>
        <h3 class="font-serif text-xl mb-3">Grace Community</h3>
        <p class="text-white/70 text-sm">A welcoming community for everyone.</p>
      </div>
      <div>
        <h4 class="font-semibold mb-3">Explore</h4>
        <ul class="space-y-2 text-white/70 text-sm">
          {{ $links ?? '' }}
        </ul>
      </div>
      <div>
        <h4 class="font-semibold mb-3">Services</h4>
        <p class="text-white/70 text-sm whitespace-pre-line">{{ $services }}</p>
        <p class="text-white/70 text-sm mt-2">{{ $address }}</p>
      </div>
      <div>
        <h4 class="font-semibold mb-3">Connect</h4>
        <p class="text-white/70 text-sm">{{ $phone }}<br>{{ $email }}</p>
        <div class="flex gap-3 mt-4">{{ $socials ?? '' }}</div>
      </div>
    </div>
    <div class="border-t border-white/10 text-center text-white/40 text-xs py-4">&copy; {{ date('Y') }} Grace Community Church</div>
  </footer>
  ```
- [ ] **Step 3:** Hero:
  ```blade
  @props(['image'=>null,'heading'=>'','sub'=>null,'primaryCta'=>null,'secondaryCta'=>null])
  <section class="relative isolate overflow-hidden">
    @if($image)<img src="{{ $image }}" alt="" class="absolute inset-0 w-full h-full object-cover">@endif
    <div class="absolute inset-0 bg-gradient-to-r from-black/70 via-black/40 to-transparent"></div>
    <div class="relative max-w-container mx-auto px-4 py-32 md:py-44 text-white">
      <h1 class="font-serif text-4xl md:text-6xl max-w-2xl leading-tight">{{ $heading }}</h1>
      @if($sub)<p class="mt-5 text-lg max-w-xl text-white/85">{{ $sub }}</p>@endif
      <div class="mt-8 flex flex-wrap gap-3">
        @if($primaryCta)<a href="{{ $primaryCta['url'] }}" class="btn-primary">{{ $primaryCta['label'] }}</a>@endif
        @if($secondaryCta)<a href="{{ $secondaryCta['url'] }}" class="btn-ghost text-white border-white/30">{{ $secondaryCta['label'] }}</a>@endif
      </div>
    </div>
  </section>
  ```
- [ ] **Step 4:** `section-heading`:
  ```blade
  @props(['eyebrow'=>null,'heading'=>'','lede'=>null,'align'=>'left'])
  <div class="@if($align==='center') text-center mx-auto max-w-2xl @endif mb-10">
    @if($eyebrow)<div class="uppercase tracking-widest text-xs text-brand-primary font-semibold mb-2">{{ $eyebrow }}</div>@endif
    <h2 class="font-serif text-3xl md:text-4xl">{{ $heading }}</h2>
    @if($lede)<p class="mt-3 text-ink-muted text-lg">{{ $lede }}</p>@endif
  </div>
  ```
- [ ] **Step 5:** `card`:
  ```blade
  @props(['image'=>null,'eyebrow'=>null,'title'=>'','meta'=>null,'href'=>'#'])
  <a href="{{ $href }}" class="card overflow-hidden block hover:-translate-y-0.5 transition">
    @if($image)<div class="aspect-[16/10] bg-cover bg-center" style="background-image:url('{{ $image }}')"></div>@endif
    <div class="p-5">
      @if($eyebrow)<div class="text-xs uppercase tracking-wider text-brand-primary mb-1">{{ $eyebrow }}</div>@endif
      <h3 class="font-serif text-xl leading-snug">{{ $title }}</h3>
      @if($meta)<div class="text-sm text-ink-muted mt-2">{{ $meta }}</div>@endif
    </div>
  </a>
  ```
- [ ] **Step 6:** `cta-band`:
  ```blade
  @props(['heading'=>'','sub'=>null,'cta'=>['url'=>'#','label'=>'Learn more']])
  <section class="bg-brand-secondary/20 my-20">
    <div class="max-w-container mx-auto px-4 py-16 md:py-20 text-center">
      <h2 class="font-serif text-3xl md:text-4xl">{{ $heading }}</h2>
      @if($sub)<p class="mt-3 text-ink-muted max-w-xl mx-auto">{{ $sub }}</p>@endif
      <a href="{{ $cta['url'] }}" class="btn-primary mt-6 inline-flex">{{ $cta['label'] }}</a>
    </div>
  </section>
  ```
- [ ] **Step 7:** `breadcrumb`:
  ```blade
  @props(['trail'=>[]])
  <nav class="text-sm text-ink-muted my-4"><ol class="flex flex-wrap gap-2">
    @foreach($trail as $i => $item)
      <li>@if($i)<span class="mx-1">/</span>@endif @if(!empty($item['url']))<a href="{{ $item['url'] }}" class="hover:text-brand-primary">{{ $item['label'] }}</a>@else <span class="text-ink">{{ $item['label'] }}</span>@endif</li>
    @endforeach
  </ol></nav>
  ```

### Task M3-T03: Home preview

**Files:**
- Create: `resources/views/preview/home.blade.php`

- [ ] **Step 1:** Layout: extend site layout. Sections: `<x-site.hero …/>`, service-times strip, "Welcome" 2-col, "Upcoming events" (3 cards from `$events`), "Latest sermon" (large + 2 small from `$sermons`), "Ministries" 4-col grid, "Latest from the blog" (3 cards from `$posts`), `<x-site.cta-band/>`, footer.
- [ ] **Step 2:** Use `<x-site.section-heading eyebrow="Upcoming" heading="Events & Gatherings" />` per section.

### Task M3-T04: About Us preview

**Files:**
- Create: `resources/views/preview/about.blade.php`

- [ ] Hero + "Our Story" 2-col + Beliefs 3-col icon grid + Leadership 4-col with placeholder photos + CTA band.

### Task M3-T05: Sermons preview (index + show)

**Files:**
- Create: `resources/views/preview/sermons/index.blade.php`
- Create: `resources/views/preview/sermons/show.blade.php`

- [ ] **Index:** Hero, filter row (series/speaker/search inputs — non-functional placeholders), grid of sermon cards using `<x-site.card>` with date + speaker meta, sidebar with "Latest series" (sticky on lg).
- [ ] **Show:** Title + meta strip, embedded `<iframe>` (YouTube placeholder), audio `<audio>` placeholder, scripture ref highlight, summary body (Tailwind `prose`), "More from this series" carousel (Alpine horizontal scroll).

### Task M3-T06: Events preview (index + show)

**Files:**
- Create: `resources/views/preview/events/index.blade.php`
- Create: `resources/views/preview/events/show.blade.php`

- [ ] **Index:** Hero, Alpine-powered tabs (Upcoming / Past), card grid with date badge overlay (large day + small month), card layout: 16:9 cover + date badge + title + location + button.
- [ ] **Show:** Hero with prominent date badge, 2-col body+sidebar (sidebar shows location + date + "Add to calendar" + "Register" external CTA), embedded Google map placeholder.

### Task M3-T07: Ministries preview (index + show)

**Files:**
- Create: `resources/views/preview/ministries/index.blade.php`
- Create: `resources/views/preview/ministries/show.blade.php`

- [ ] **Index:** Hero + tile grid (3-col, image + name + summary + "Learn more" link).
- [ ] **Show:** Hero, body (prose), leader card with photo + name + role + contact email button.

### Task M3-T08: Blog preview (index + show)

**Files:**
- Create: `resources/views/preview/blog/index.blade.php`
- Create: `resources/views/preview/blog/show.blade.php`

- [ ] **Index:** Hero, category chip filter row, featured post (large 2-col), grid of cards, pagination placeholder.
- [ ] **Show:** Title + meta (author + date + category chip), featured image, body using Tailwind `prose prose-lg`, author block with avatar + bio, "Related posts" 3-col.

### Task M3-T09: Contact preview

**Files:**
- Create: `resources/views/preview/contact.blade.php`

- [ ] Hero + 2-col: left form (name/email/phone/subject/message + button — submit disabled), right info card (address, phone, email, service times) + embedded Google Maps `<iframe>` placeholder.

### Task M3-T10: SIGN-OFF GATE — public site UI/UX review

- [ ] **Step 1:** Stop and tell the user: "Public site mockup is live at http://127.0.0.1:8000/preview. Please walk through every page (Home, About, Sermons + detail, Events + detail, Ministries + detail, Blog + detail, Contact) on desktop and mobile widths. Note any visual changes you want before I wire to the database."
- [ ] **Step 2:** Iterate on feedback. Update components/views.
- [ ] **Step 3:** Wait for explicit "approved — move on" from user before starting M4.

---

# Milestone 4 — Admin Panel UI/UX (static `/admin/preview/*`) — SIGN-OFF GATE

### Task M4-T01: Admin route group + admin layout

**Files:**
- Modify: `routes/preview.php` (add admin preview)
- Create: `resources/views/components/admin/layout.blade.php`
- Create: `resources/views/components/admin/sidebar.blade.php`
- Create: `resources/views/components/admin/topbar.blade.php`

- [ ] **Step 1:** Add to `routes/preview.php`:
  ```php
  Route::prefix('admin/preview')->name('admin.preview.')->group(function () {
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
  ```
- [ ] **Step 2:** Admin layout shell component:
  ```blade
  @props(['title'=>'Admin'])
  <!DOCTYPE html>
  <html lang="en">
  <head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }} | Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css','resources/js/app.js'])
    @stack('head')
  </head>
  <body class="bg-[#F7F5F1] font-sans">
    <div x-data="{sidebar:false}" class="flex min-h-screen">
      <x-admin.sidebar />
      <div class="flex-1 lg:ml-64 flex flex-col min-h-screen">
        <x-admin.topbar />
        <main class="flex-1 p-6 lg:p-8">{{ $slot }}</main>
      </div>
    </div>
    @stack('scripts')
  </body>
  </html>
  ```
- [ ] **Step 3:** Sidebar (with all menu items; active state by route name):
  ```blade
  @php
  $links = [
    ['route'=>'admin.preview.dashboard','label'=>'Dashboard','icon'=>'home'],
    ['route'=>'admin.preview.pages','label'=>'Pages','icon'=>'doc'],
    ['route'=>'admin.preview.blog','label'=>'Blog','icon'=>'pen'],
    ['route'=>'admin.preview.sermons','label'=>'Sermons','icon'=>'mic'],
    ['route'=>'admin.preview.events','label'=>'Events','icon'=>'calendar'],
    ['route'=>'admin.preview.ministries','label'=>'Ministries','icon'=>'users'],
    ['route'=>'admin.preview.media','label'=>'Media','icon'=>'image'],
    ['route'=>'admin.preview.menus','label'=>'Menus','icon'=>'list'],
    ['route'=>'admin.preview.messages','label'=>'Messages','icon'=>'mail'],
    ['route'=>'admin.preview.users','label'=>'Users','icon'=>'user'],
    ['route'=>'admin.preview.roles','label'=>'Roles','icon'=>'shield'],
    ['route'=>'admin.preview.settings','label'=>'Settings','icon'=>'cog'],
  ];
  @endphp
  <aside :class="sidebar?'translate-x-0':'-translate-x-full'" class="fixed lg:translate-x-0 lg:static z-40 inset-y-0 left-0 w-64 bg-[#1F1A18] text-[#FAF7F2] transition-transform">
    <div class="px-6 py-5 text-lg font-serif border-b border-white/10">Church CMS</div>
    <nav class="py-3">
      @foreach($links as $l)
        <a href="{{ route($l['route']) }}" @class([
          'flex items-center gap-3 px-6 py-2.5 text-sm hover:bg-white/5',
          'bg-white/5 border-l-2 border-brand-secondary' => request()->routeIs($l['route']),
        ])>
          <span class="w-4 h-4 inline-block bg-current/30 rounded"></span>{{ $l['label'] }}
        </a>
      @endforeach
    </nav>
  </aside>
  ```
- [ ] **Step 4:** Topbar (breadcrumb + user dropdown):
  ```blade
  <header class="h-14 bg-white border-b border-[rgb(var(--border))] flex items-center px-4 lg:px-8 justify-between">
    <div class="flex items-center gap-3">
      <button class="lg:hidden p-2" @click="sidebar=!sidebar">&#9776;</button>
      <span class="text-sm text-ink-muted">{{ request()->routeIs('admin.preview.dashboard') ? 'Dashboard' : ucfirst(explode('.',Route::currentRouteName())[2] ?? '') }}</span>
    </div>
    <div x-data="{open:false}" class="relative">
      <button @click="open=!open" class="flex items-center gap-2 text-sm">Site Admin <svg class="w-3 h-3" viewBox="0 0 20 20" fill="currentColor"><path d="M5.25 7.5 10 12.25 14.75 7.5"/></svg></button>
      <div x-show="open" @click.away="open=false" x-cloak class="absolute right-0 mt-2 w-44 card p-2 text-sm">
        <a href="#" class="block px-3 py-1.5 hover:bg-surface rounded">Profile</a>
        <a href="#" class="block px-3 py-1.5 hover:bg-surface rounded">Sign out</a>
      </div>
    </div>
  </header>
  ```

### Task M4-T02: Admin login preview

**Files:**
- Create: `resources/views/preview/admin/login.blade.php`

- [ ] Split layout: left panel `bg-brand-primary text-white` with church name + verse quote; right panel white card with email + password + button + forgot link.

### Task M4-T03: Admin dashboard preview

**Files:**
- Create: `resources/views/preview/admin/dashboard.blade.php`

- [ ] **Step 1:** 4 KPI cards (Pages: 7 / Published posts: 12 / Upcoming events: 5 / Unread messages: 3) in a grid.
- [ ] **Step 2:** Two-col below: recent activity list (10 hard-coded entries) + quick-action panel (New post / New event / Edit homepage).

### Task M4-T04: Admin "Pages" preview (index + edit)

**Files:**
- Create: `resources/views/preview/admin/pages/index.blade.php`
- Create: `resources/views/preview/admin/pages/edit.blade.php`

- [ ] **Index:** page header ("Pages") with no "New" button (singletons), table: Title / Slug / Status pill / Last edited by / Updated / Actions.
- [ ] **Edit:** Tabbed (`x-data="{tab:'content'}"`) — Content / SEO / Settings.
  - **Content tab:** hero fields + a CKEditor placeholder (`<textarea id="body" rows="14" class="input">`) + Sections builder (Alpine sortable list with "Add section" dropdown placeholder).
  - **SEO tab:** meta title / description / og image picker.
  - **Settings tab:** publish toggle + datepicker + revision history button.
  - Top-right action bar: "Save Draft" / "Save & Publish" buttons. "Last saved 2 min ago" auto-save indicator placeholder.

### Task M4-T05: Admin "Blog" preview

**Files:**
- Create: `resources/views/preview/admin/blog/index.blade.php`
- Create: `resources/views/preview/admin/blog/edit.blade.php`

- [ ] **Index:** Filter row (status / category / search) + "+ New Post" button + table (title, category, author, status, published_at, actions).
- [ ] **Edit:** title input, slug input, category select, excerpt textarea, CKEditor placeholder for body, featured image picker (button "Choose from media library" → opens media modal placeholder), SEO tab, publish toggle, scheduled `published_at`.

### Task M4-T06: Admin "Sermons" preview

**Files:**
- Create: `resources/views/preview/admin/sermons/index.blade.php`

- [ ] **Step 1:** Tabbed sub-nav: Sermons / Series / Speakers. Each tab shows a CRUD table.
- [ ] **Step 2:** Sermons table cols: title, series, speaker, preached_on, status, actions. "+ New sermon" button.

### Task M4-T07: Admin "Events", "Ministries", "Messages", "Users", "Roles", "Menus", "Settings" preview

**Files:** one Blade view per route from M4-T01 step 1 (e.g. `resources/views/preview/admin/events/index.blade.php`).

- [ ] **Step 1 (Events):** Tabs Upcoming/Past, "+ New event" button, table with date badge + title + location + status.
- [ ] **Step 2 (Ministries):** Drag-handle sortable list with cover + name + leader + actions.
- [ ] **Step 3 (Messages):** Inbox list (sender, subject, snippet, age) + side detail panel.
- [ ] **Step 4 (Users):** Table (name, email, role badge, last_login, status, actions: edit / activate / send reset).
- [ ] **Step 5 (Roles):** Card per role. Inside: name + permission checkboxes grouped (Pages / Blog / Sermons / Events / Ministries / Media / Settings / Users).
- [ ] **Step 6 (Menus):** Pick menu dropdown (Main / Footer) + nested sortable list. "Add item" button → modal placeholder.
- [ ] **Step 7 (Settings):** Tabs Branding / Contact / Social / Footer / SEO / Mail. Each tab shows fields per `SiteSettingsSeeder` defaults. Branding tab has color inputs `<input type="color">` for the 2 brand colors.

### Task M4-T08: Admin "Media" preview

**Files:**
- Create: `resources/views/preview/admin/media.blade.php`

- [ ] **Step 1:** Two-col layout: left = folder tree (placeholder ul/li), right = thumbnail grid (12 placeholder squares). Top toolbar: search + "Upload" button + view-toggle.
- [ ] **Step 2:** Hover state on thumbnail shows alt-text + filename + actions (rename/delete).

### Task M4-T09: SIGN-OFF GATE — admin UI/UX review

- [ ] **Step 1:** Tell the user: "Admin mockup is live at `/admin/preview` (login at `/admin/preview/login`). Please walk through every screen on desktop and mobile widths. Note any visual changes."
- [ ] **Step 2:** Iterate on feedback.
- [ ] **Step 3:** Wait for explicit approval before starting M5.

---

# Milestone 5 — Real Auth (admin + member)

### Task M5-T01: Configure 2 guards

**Files:**
- Modify: `config/auth.php`

- [ ] **Step 1:** Inside `guards`:
  ```php
  'admin' => ['driver'=>'session','provider'=>'users'],
  ```
  (`web` already exists.)
- [ ] **Step 2:** Inside `passwords`:
  ```php
  'admins' => [
      'provider'=>'users','table'=>'password_reset_tokens',
      'expire'=>30,'throttle'=>60,
  ],
  ```

### Task M5-T02: Spatie permission config — admin guard default

**Files:**
- Modify: `config/permission.php`

- [ ] In `models.permission` and `models.role`, no change. Set `'register_permission_check_method' => true`. Ensure middleware aliases registered.
- [ ] In `app/Http/Kernel.php` (Laravel 12 uses `bootstrap/app.php` for middleware now), register Spatie aliases:
  ```php
  // bootstrap/app.php in withMiddleware()
  $middleware->alias([
      'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
      'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
      'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
  ]);
  ```

### Task M5-T03: Auth controllers — admin login

**Files:**
- Create: `app/Http/Controllers/Auth/AdminLoginController.php`
- Create: `app/Http/Requests/Auth/AdminLoginRequest.php`
- Create: `resources/views/auth/admin/login.blade.php` (move/copy from `preview/admin/login.blade.php`)
- Modify: `routes/auth.php` (new) and `routes/web.php`

- [ ] **Step 1:** `AdminLoginRequest`:
  ```php
  <?php
  namespace App\Http\Requests\Auth;
  use Illuminate\Foundation\Http\FormRequest;
  use Illuminate\Validation\ValidationException;
  use Illuminate\Support\Facades\{Auth,RateLimiter};

  class AdminLoginRequest extends FormRequest {
      public function authorize(): bool { return true; }
      public function rules(): array {
          return ['email'=>'required|email','password'=>'required|string'];
      }
      public function authenticate(): void {
          $this->ensureIsNotRateLimited();
          if (!Auth::guard('admin')->attempt(
              ['email'=>$this->email,'password'=>$this->password,'is_admin'=>true]
          )) {
              RateLimiter::hit($this->throttleKey(),60);
              throw ValidationException::withMessages(['email'=>__('auth.failed')]);
          }
          RateLimiter::clear($this->throttleKey());
      }
      protected function ensureIsNotRateLimited(): void {
          if (!RateLimiter::tooManyAttempts($this->throttleKey(),5)) return;
          throw ValidationException::withMessages([
              'email'=>'Too many login attempts. Try again in '.RateLimiter::availableIn($this->throttleKey()).' seconds.',
          ]);
      }
      public function throttleKey(): string {
          return strtolower($this->email).'|'.$this->ip().'|admin-login';
      }
  }
  ```
- [ ] **Step 2:** `AdminLoginController`:
  ```php
  <?php
  namespace App\Http\Controllers\Auth;
  use App\Http\Controllers\Controller;
  use App\Http\Requests\Auth\AdminLoginRequest;
  use Illuminate\Http\Request;
  use Illuminate\Support\Facades\Auth;

  class AdminLoginController extends Controller {
      public function create() { return view('auth.admin.login'); }
      public function store(AdminLoginRequest $req) {
          $req->authenticate();
          $req->session()->regenerate();
          return redirect()->intended(route('admin.dashboard'));
      }
      public function destroy(Request $req) {
          Auth::guard('admin')->logout();
          $req->session()->invalidate();
          $req->session()->regenerateToken();
          return redirect()->route('admin.login');
      }
  }
  ```
- [ ] **Step 3:** Create `routes/auth.php` and include from `web.php`:
  ```php
  use App\Http\Controllers\Auth\AdminLoginController;
  Route::middleware('guest:admin')->group(function () {
      Route::get('/admin/login', [AdminLoginController::class,'create'])->name('admin.login');
      Route::post('/admin/login', [AdminLoginController::class,'store']);
  });
  Route::middleware('auth:admin')->group(function () {
      Route::post('/admin/logout', [AdminLoginController::class,'destroy'])->name('admin.logout');
  });
  ```
- [ ] **Step 4:** Convert `preview/admin/login.blade.php` to `resources/views/auth/admin/login.blade.php`. Replace the form to POST to `route('admin.login')` with `@csrf`, bind `old('email')`, show `$errors->get('email')`.
- [ ] **Step 5:** Manual smoke: `php artisan migrate:fresh --seed`, then visit `/admin/login`, log in with seeded admin (`ADMIN_EMAIL`/`ADMIN_PASSWORD` from `.env`). Should land on `admin.dashboard` (we'll wire that view next).

### Task M5-T04: Member login (placeholder)

**Files:**
- Create: `app/Http/Controllers/Auth/MemberLoginController.php`
- Create: `app/Http/Requests/Auth/MemberLoginRequest.php`
- Create: `resources/views/auth/member/login.blade.php`
- Create: `resources/views/member/coming-soon.blade.php`
- Modify: `routes/auth.php`, `routes/member.php` (new)

- [ ] **Step 1:** Mirror `AdminLoginRequest` but use `web` guard, drop `is_admin` from attempt array (members are `is_admin=false`), throttle key suffix `member-login`.
- [ ] **Step 2:** Mirror controller; on success redirect to `route('member.dashboard')`.
- [ ] **Step 3:** Create `routes/member.php`:
  ```php
  use App\Http\Controllers\Auth\MemberLoginController;
  Route::middleware('guest:web')->group(function () {
      Route::get('/login', [MemberLoginController::class,'create'])->name('login');
      Route::post('/login', [MemberLoginController::class,'store']);
  });
  Route::middleware('auth:web')->group(function () {
      Route::post('/logout', [MemberLoginController::class,'destroy'])->name('logout');
      Route::view('/member', 'member.coming-soon')->name('member.dashboard');
  });
  ```
- [ ] **Step 4:** Include `member.php` and `auth.php` from `web.php`.
- [ ] **Step 5:** `coming-soon.blade.php` just shows "Member portal coming soon" message with logout button.

### Task M5-T05: Password reset (both guards)

**Files:**
- Create: `app/Http/Controllers/Auth/PasswordResetController.php`
- Create: `resources/views/auth/admin/forgot.blade.php`, `auth/admin/reset.blade.php`
- Create: `resources/views/auth/member/forgot.blade.php`, `auth/member/reset.blade.php`
- Modify: `routes/auth.php`

- [ ] **Step 1:** Implement single controller with `forAdmin()` and `forMember()` methods using `Password::broker('admins')` vs `Password::broker('users')`. Each has `showLinkRequestForm`, `sendResetLinkEmail`, `showResetForm`, `reset`.
- [ ] **Step 2:** Rate-limit `sendResetLinkEmail` (3/hour/IP) via `RateLimiter::for('password-reset', …)` in `RouteServiceProvider` or `bootstrap/app.php`.
- [ ] **Step 3:** Email uses Laravel's signed reset URL (default behavior).
- [ ] **Step 4:** Add routes:
  ```php
  Route::middleware('guest:admin')->group(function () {
      Route::get('/admin/forgot', [PasswordResetController::class,'showLinkRequestAdmin'])->name('admin.password.request');
      Route::post('/admin/forgot', [PasswordResetController::class,'sendResetLinkAdmin'])->name('admin.password.email');
      Route::get('/admin/reset/{token}', [PasswordResetController::class,'showResetAdmin'])->name('admin.password.reset');
      Route::post('/admin/reset', [PasswordResetController::class,'resetAdmin'])->name('admin.password.update');
  });
  // mirror for members
  ```

### Task M5-T06: Middleware — EnsureIsAdmin

**Files:**
- Create: `app/Http/Middleware/EnsureIsAdmin.php`
- Modify: `bootstrap/app.php`

- [ ] **Step 1:**
  ```php
  <?php
  namespace App\Http\Middleware;
  use Closure;
  use Illuminate\Http\Request;
  use Symfony\Component\HttpFoundation\Response;

  class EnsureIsAdmin {
      public function handle(Request $request, Closure $next): Response {
          if (!auth('admin')->check() || !auth('admin')->user()->is_admin) {
              abort(403);
          }
          return $next($request);
      }
  }
  ```
- [ ] **Step 2:** Register alias in `bootstrap/app.php`: `$middleware->alias(['admin' => \App\Http\Middleware\EnsureIsAdmin::class])`.

---

# Milestone 6 — Public Site Wired to DB

### Task M6-T01: Settings repository (cached)

**Files:**
- Create: `app/Services/SettingsRepository.php`
- Create: `app/Providers/SettingsServiceProvider.php`
- Modify: `bootstrap/providers.php`

- [ ] **Step 1:**
  ```php
  <?php
  namespace App\Services;
  use App\Models\SiteSetting;
  use Illuminate\Support\Facades\Cache;

  class SettingsRepository {
      protected const KEY = 'site_settings';
      public function all(): array {
          return Cache::rememberForever(self::KEY, fn () =>
              SiteSetting::pluck('value','key')->toArray()
          );
      }
      public function get(string $key, mixed $default = null): mixed {
          return $this->all()[$key] ?? $default;
      }
      public function set(string $key, mixed $value): void {
          SiteSetting::updateOrCreate(['key'=>$key],['value'=>$value]);
          Cache::forget(self::KEY);
      }
      public function flush(): void { Cache::forget(self::KEY); }
  }
  ```
- [ ] **Step 2:** Register singleton + a `settings()` helper in a provider:
  ```php
  $this->app->singleton(SettingsRepository::class);
  ```
- [ ] **Step 3:** Add helper in `app/helpers.php` (autoloaded via `composer.json` `autoload.files`):
  ```php
  if (!function_exists('settings')) {
      function settings(?string $key=null, mixed $default=null) {
          $repo = app(\App\Services\SettingsRepository::class);
          return $key === null ? $repo : $repo->get($key, $default);
      }
  }
  ```
- [ ] **Step 4:** Add to `composer.json`:
  ```json
  "autoload": { "files": ["app/helpers.php"], "psr-4": { "App\\": "app/" } }
  ```
  Then `composer dump-autoload`.

### Task M6-T02: Brand-token hydration middleware

**Files:**
- Create: `app/Http/Middleware/HydrateBrandTokens.php`
- Modify: `resources/views/layouts/site.blade.php`
- Modify: `bootstrap/app.php`

- [ ] **Step 1:** Middleware reads `settings('brand.color.primary')` etc., converts hex to RGB triples, and shares them as Blade variables:
  ```php
  public function handle($request, $next) {
      $hex2rgb = fn ($h) => sscanf(ltrim($h,'#'), '%02x%02x%02x');
      $primary = $hex2rgb(settings('brand.color.primary','#7A1F2B'));
      $secondary = $hex2rgb(settings('brand.color.secondary','#C9A961'));
      view()->share('brandPrimaryRgb', implode(' ',$primary));
      view()->share('brandSecondaryRgb', implode(' ',$secondary));
      return $next($request);
  }
  ```
- [ ] **Step 2:** In `site.blade.php` and `admin.blade.php`, inside `<head>`:
  ```blade
  <style>:root { --brand-primary: {{ $brandPrimaryRgb }}; --brand-secondary: {{ $brandSecondaryRgb }}; }</style>
  ```
- [ ] **Step 3:** Register in `bootstrap/app.php` web middleware group:
  ```php
  $middleware->web(append: [\App\Http\Middleware\HydrateBrandTokens::class]);
  ```

### Task M6-T03: NavComposer for main+footer menus

**Files:**
- Create: `app/Http/ViewComposers/NavComposer.php`
- Create: `app/Providers/ViewComposerServiceProvider.php`

- [ ] **Step 1:**
  ```php
  <?php
  namespace App\Http\ViewComposers;
  use Illuminate\View\View;
  use App\Models\Menu;

  class NavComposer {
      public function compose(View $view): void {
          $main = Menu::with(['items'=>fn($q)=>$q->whereNull('parent_id')->orderBy('sort_order')->with('children')])
              ->where('slug','main')->first();
          $items = $main ? $main->items->map(fn($i)=>[
              'label'=>$i->label,
              'url'=>$this->resolveUrl($i),
          ])->all() : [];
          $view->with('siteNav', $items);
      }
      protected function resolveUrl($i): string {
          return match($i->link_type) {
              'route' => route($i->link_value, [], false),
              'url' => $i->link_value,
              'page' => route('site.page', ['slug'=>$i->link_value]),
          };
      }
  }
  ```
- [ ] **Step 2:** Provider:
  ```php
  public function boot(): void {
      View::composer(['layouts.site','components.site.header','components.site.footer'], NavComposer::class);
  }
  ```
- [ ] **Step 3:** Add `MenuItem::children` relation: `$this->hasMany(MenuItem::class,'parent_id')->orderBy('sort_order')`.
- [ ] **Step 4:** Register provider in `bootstrap/providers.php`.

### Task M6-T04: Site routes + controllers

**Files:**
- Create: `app/Http/Controllers/Site/PageController.php`
- Create: `app/Http/Controllers/Site/BlogController.php`
- Create: `app/Http/Controllers/Site/SermonController.php`
- Create: `app/Http/Controllers/Site/EventController.php`
- Create: `app/Http/Controllers/Site/MinistryController.php`
- Create: `app/Http/Controllers/Site/ContactController.php`
- Modify: `routes/web.php`

- [ ] **Step 1:** Routes:
  ```php
  use App\Http\Controllers\Site\{PageController,BlogController,SermonController,EventController,MinistryController,ContactController};

  Route::name('site.')->group(function () {
      Route::get('/', [PageController::class,'home'])->name('home');
      Route::get('/about', [PageController::class,'about'])->name('about');
      Route::get('/contact', [ContactController::class,'show'])->name('contact');
      Route::post('/contact', [ContactController::class,'submit'])->name('contact.submit');

      Route::get('/sermons', [SermonController::class,'index'])->name('sermons.index');
      Route::get('/sermons/{sermon:slug}', [SermonController::class,'show'])->name('sermons.show');

      Route::get('/events', [EventController::class,'index'])->name('events.index');
      Route::get('/events/{event:slug}', [EventController::class,'show'])->name('events.show');

      Route::get('/ministries', [MinistryController::class,'index'])->name('ministries.index');
      Route::get('/ministries/{ministry:slug}', [MinistryController::class,'show'])->name('ministries.show');

      Route::get('/blog', [BlogController::class,'index'])->name('blog.index');
      Route::get('/blog/{post:slug}', [BlogController::class,'show'])->name('blog.show');
  });
  ```
- [ ] **Step 2:** `PageController::home`:
  ```php
  public function home() {
      $page = \App\Models\Page::where('slug','home')->published()->firstOrFail();
      $upcomingEvents = \App\Models\Event::published()->where('starts_at','>=',now())->orderBy('starts_at')->limit(3)->get();
      $latestSermon = \App\Models\Sermon::published()->latest('preached_on')->first();
      $morePosts = \App\Models\BlogPost::published()->latest('published_at')->limit(3)->get();
      $ministries = \App\Models\Ministry::published()->orderBy('sort_order')->limit(6)->get();
      return view('site.home', compact('page','upcomingEvents','latestSermon','morePosts','ministries'));
  }
  public function about() {
      $page = \App\Models\Page::where('slug','about-us')->published()->firstOrFail();
      return view('site.about', compact('page'));
  }
  ```
  Add the `published()` scope to `Page`, `BlogPost`, `Sermon`, `Event`, `Ministry`.
- [ ] **Step 3:** Other controllers follow same pattern: index returns paginated published collection; show returns single record by slug or 404 if unpublished.

### Task M6-T05: Convert preview views to real site views

**Files:**
- Move/copy `resources/views/preview/home.blade.php` → `resources/views/site/home.blade.php` and replace hard-coded arrays with `$page`, `$upcomingEvents`, etc.
- Same for about, sermons/*, events/*, ministries/*, blog/*, contact.

- [ ] **Step 1:** In `home.blade.php`, replace placeholders with `$page->hero_heading`, `$page->hero_subheading`, `{!! $page->body !!}` (purified HTML), and loop `$upcomingEvents`, `$morePosts`, `$ministries` using `<x-site.card>`.
- [ ] **Step 2:** Repeat per page. For events show: `format start_at`, render description with `{!! $event->description !!}`.
- [ ] **Step 3:** Add `.ics` calendar generation for events — `EventController::ics($event)` returns `text/calendar` content with `DTSTART`/`DTEND` from `$event`.
- [ ] **Step 4:** After wiring each page, manually visit `/`, `/about`, `/sermons`, `/sermons/{slug}`, `/events`, `/events/{slug}`, `/ministries`, `/ministries/{slug}`, `/blog`, `/blog/{slug}`, `/contact`. Confirm content from seeded DB renders.

### Task M6-T06: Disable preview routes outside local

**Files:**
- Modify: `routes/web.php`

- [ ] Ensure preview route inclusion only triggers when `app()->environment(['local','testing'])`. Add comment so the engineer knows it's intentional.

---

# Milestone 7 — Admin CRUD wired to DB

> Every controller in this milestone follows the same shape: extends `App\Http\Controllers\Admin\Controller` (a base controller you create in the first task), uses `FormRequest` for validation, calls a small `Action`/service for persistence, redirects with flash message. Each resource gets full CRUD: index, create, store, edit, update, destroy.

### Task M7-T01: Admin base controller + admin route group

**Files:**
- Create: `app/Http/Controllers/Admin/Controller.php`
- Modify: `routes/admin.php` (new)
- Modify: `routes/web.php` to include `admin.php`
- Create: `resources/views/admin/dashboard.blade.php`
- Create: `app/Http/Controllers/Admin/DashboardController.php`

- [ ] **Step 1:** Base controller is empty subclass of `App\Http\Controllers\Controller`. Optional: add `flash()` helper.
- [ ] **Step 2:** `routes/admin.php`:
  ```php
  <?php
  use Illuminate\Support\Facades\Route;
  use App\Http\Controllers\Admin\{DashboardController, PageController, BlogPostController, BlogCategoryController, SermonController, SermonSeriesController, SermonSpeakerController, EventController, MinistryController, MediaController, MediaFolderController, MenuController, MenuItemController, MessageController, UserController, RoleController, SettingsController};

  Route::middleware(['auth:admin','admin'])->prefix('admin')->name('admin.')->group(function () {
      Route::get('/', [DashboardController::class,'index'])->name('dashboard');
      Route::resource('pages', PageController::class)->except(['create','destroy'])->middleware('permission:manage-pages');
      Route::resource('blog/categories', BlogCategoryController::class)->middleware('permission:manage-blog');
      Route::resource('blog/posts', BlogPostController::class)->middleware('permission:manage-blog');
      Route::resource('sermons/series', SermonSeriesController::class)->middleware('permission:manage-sermons');
      Route::resource('sermons/speakers', SermonSpeakerController::class)->middleware('permission:manage-sermons');
      Route::resource('sermons', SermonController::class)->middleware('permission:manage-sermons');
      Route::resource('events', EventController::class)->middleware('permission:manage-events');
      Route::resource('ministries', MinistryController::class)->middleware('permission:manage-ministries');
      Route::get('media', [MediaController::class,'index'])->name('media.index')->middleware('permission:manage-media');
      Route::post('media', [MediaController::class,'store'])->name('media.store')->middleware('permission:manage-media');
      Route::post('media/folders', [MediaFolderController::class,'store'])->name('media.folders.store')->middleware('permission:manage-media');
      Route::delete('media/{media}', [MediaController::class,'destroy'])->name('media.destroy')->middleware('permission:manage-media');
      Route::resource('menus', MenuController::class)->middleware('permission:manage-menus');
      Route::resource('menus.items', MenuItemController::class)->shallow()->middleware('permission:manage-menus');
      Route::get('messages', [MessageController::class,'index'])->name('messages.index')->middleware('permission:manage-messages');
      Route::get('messages/{message}', [MessageController::class,'show'])->name('messages.show')->middleware('permission:manage-messages');
      Route::resource('users', UserController::class)->middleware('permission:manage-users');
      Route::resource('roles', RoleController::class)->middleware('permission:manage-roles');
      Route::get('settings', [SettingsController::class,'index'])->name('settings.index')->middleware('permission:manage-settings');
      Route::post('settings', [SettingsController::class,'update'])->name('settings.update')->middleware('permission:manage-settings');
  });
  ```
- [ ] **Step 3:** `DashboardController::index` returns view with KPI numbers + recent `ActivityLog` entries.
- [ ] **Step 4:** Copy `preview/admin/dashboard.blade.php` to `admin/dashboard.blade.php` and replace hard-coded values with controller-supplied vars.

### Task M7-T02: Pages CRUD

**Files:**
- Create: `app/Http/Controllers/Admin/PageController.php`
- Create: `app/Http/Requests/Admin/PageRequest.php`
- Create: `app/Services/PagePublisher.php`
- Create: `app/Policies/PagePolicy.php`
- Modify: `app/Providers/AppServiceProvider.php` to register Gate::before
- Create: `resources/views/admin/pages/index.blade.php`, `edit.blade.php`

- [ ] **Step 1:** `PageRequest`:
  ```php
  public function rules(): array {
      $id = $this->route('page')?->id;
      return [
          'title' => 'required|string|max:200',
          'slug' => "required|alpha_dash|max:200|unique:pages,slug,$id",
          'hero_heading' => 'nullable|string|max:200',
          'hero_subheading' => 'nullable|string|max:500',
          'hero_image_path' => 'nullable|string',
          'body' => 'nullable|string',
          'meta_title' => 'nullable|string|max:200',
          'meta_description' => 'nullable|string|max:500',
          'is_published' => 'boolean',
      ];
  }
  ```
- [ ] **Step 2:** `PagePublisher`:
  ```php
  public function save(Page $page, array $data, ?User $actor=null): Page {
      $data['body'] = $this->purify($data['body'] ?? '');
      if (!empty($data['is_published']) && !$page->published_at) $data['published_at'] = now();
      $data['updated_by'] = $actor?->id;
      $page->fill($data)->save();
      PageRevision::create([
          'page_id'=>$page->id,
          'snapshot'=>json_encode($page->fresh()->toArray()),
          'user_id'=>$actor?->id,
      ]);
      return $page->fresh();
  }
  protected function purify(string $html): string { return clean($html,'cms'); }
  ```
- [ ] **Step 3:** In `config/purifier.php`, define a `cms` profile that allows the CKEditor toolbar tags/attrs (b, i, u, strong, em, p, br, ul, ol, li, blockquote, a[href|target|rel], h2, h3, h4, img[src|alt|width|height], table, thead, tbody, tr, td, th, iframe[src|width|height|allow|allowfullscreen], span[style], font[face]).
- [ ] **Step 4:** `PageController`:
  ```php
  public function index() { return view('admin.pages.index', ['pages'=>Page::orderBy('title')->paginate(20)]); }
  public function edit(Page $page) { return view('admin.pages.edit', compact('page')); }
  public function update(PageRequest $req, Page $page, PagePublisher $svc) {
      $svc->save($page, $req->validated(), $req->user('admin'));
      return back()->with('success','Page updated');
  }
  public function store(PageRequest $req, PagePublisher $svc) {
      $page = new Page;
      $svc->save($page, $req->validated(), $req->user('admin'));
      return redirect()->route('admin.pages.edit', $page)->with('success','Page created');
  }
  ```
- [ ] **Step 5:** `PagePolicy` — every method returns `$user->can('manage-pages')`.
- [ ] **Step 6:** `AppServiceProvider::boot`:
  ```php
  Gate::before(fn ($user) => $user->hasRole('Site Admin') ? true : null);
  ```
- [ ] **Step 7:** Convert the preview Pages views to real admin views (replace hard-coded data with controller-supplied `$pages` and `$page`).

### Task M7-T03: Blog CRUD (categories + posts)

**Files:** mirror M7-T02 for `BlogCategory` and `BlogPost`.

- [ ] **Step 1:** Categories: minimal (slug auto, name required, unique).
- [ ] **Step 2:** Posts: title, slug auto, excerpt, body (CKEditor), category_id, author_id (auto = current admin), featured_image_path, meta_title/desc, is_published, published_at.
- [ ] **Step 3:** Same `PagePublisher`-style save pattern with HTMLPurifier on `body`.
- [ ] **Step 4:** Convert preview views.

### Task M7-T04: Sermons CRUD (series, speakers, sermons)

- [ ] Mirror prior shape. Sermon `video_url` accepts YouTube/Vimeo URL — store as-is; show view embeds based on parsing.
- [ ] Convert preview views.

### Task M7-T05: Events CRUD

- [ ] Date/time inputs (Flatpickr — `npm install flatpickr`, import in `app.js`). Validate `ends_at` >= `starts_at` or null.
- [ ] Convert preview views.

### Task M7-T06: Ministries CRUD

- [ ] Standard CRUD + drag-sort endpoint that accepts `[ids]` and re-saves `sort_order`. POST `/admin/ministries/reorder`.
- [ ] Convert preview views.

### Task M7-T07: Menus CRUD

**Files:**
- Create: `app/Http/Controllers/Admin/MenuController.php`, `MenuItemController.php`
- Create: `resources/views/admin/menus/index.blade.php`, `edit.blade.php`

- [ ] **Step 1:** Editor uses SortableJS (`npm install sortablejs`) to drag-sort and nest one level. On save, POST a flat array of `[id, parent_id, sort_order]` rows; controller iterates and updates.
- [ ] **Step 2:** "Add item" modal lets user pick: existing page (dropdown of `Page::pluck('title','slug')`), custom URL, or named route (dropdown of `site.*` routes).

### Task M7-T08: Users + Roles CRUD

- [ ] **Step 1:** Users index: list + filter by role + admin-only. Create/edit form: name, email (unique), password (only on create or "reset password" button), role multi-select.
- [ ] **Step 2:** Roles index: cards per role. Create/edit form: name (read-only on built-in roles), grouped permission checkboxes (group by prefix before the hyphen).
- [ ] **Step 3:** Block deleting/editing the `Site Admin` role and its primary admin user (UI hides the actions, controllers reject).

### Task M7-T09: Settings page

**Files:**
- Create: `app/Http/Controllers/Admin/SettingsController.php`
- Create: `app/Http/Requests/Admin/SettingsRequest.php`
- Create: `resources/views/admin/settings/index.blade.php`

- [ ] **Step 1:** Tabs per group (brand/contact/social/footer/seo/mail). One `POST /admin/settings` handler processes all groups in a single multipart submit.
- [ ] **Step 2:** Brand color inputs are `<input type="color">`. Logo + favicon use the media-library modal.
- [ ] **Step 3:** Controller iterates expected keys, calls `settings()->set($key, $value)` for each, returns back with flash.

### Task M7-T10: Messages inbox

- [ ] **Step 1:** Index = paginated list of `ContactMessage` with filters (read/unread). Click → show view + auto-mark `read_at = now()`.
- [ ] **Step 2:** Detail page shows full message + "Reply via email" mailto + delete button.

---

# Milestone 8 — Media Library + CKEditor

### Task M8-T01: Storage symlink + image processing

**Files:**
- Run: `php artisan storage:link`
- Modify: `config/filesystems.php` ensure `public` disk root is correct
- Modify: `bootstrap/providers.php` to register `Intervention\Image\Laravel\ServiceProvider`

- [ ] Confirm `public/storage` symlink exists. Test by writing a file to `storage/app/public/test.txt` and visiting `/storage/test.txt`.

### Task M8-T02: MediaUploader service

**Files:**
- Create: `app/Services/MediaUploader.php`

- [ ] **Step 1:**
  ```php
  use Intervention\Image\Laravel\Facades\Image;
  use Illuminate\Support\Str;

  class MediaUploader {
      public function store(UploadedFile $file, ?MediaFolder $folder=null, ?User $actor=null): Media {
          $this->guard($file);
          $ext = strtolower($file->getClientOriginalExtension());
          $name = Str::random(8).'_'.Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)).'.'.$ext;
          $dir = trim(($folder?->path ?? 'uploads'), '/');
          $path = "$dir/$name";
          $bytes = $file->getContent();
          if (in_array($file->getMimeType(), ['image/jpeg','image/png','image/webp'])) {
              $img = Image::read($bytes);
              if ($img->width() > 2400) $img->scaleDown(width: 2400);
              $bytes = (string) $img->toJpeg(quality: 85);
              if ($ext !== 'jpg' && $ext !== 'jpeg') { $path = preg_replace('/\.\w+$/','.jpg',$path); $ext = 'jpg'; }
          }
          Storage::disk('public')->put($path, $bytes);
          [$w,$h] = @getimagesizefromstring($bytes) ?: [null,null];
          return Media::create([
              'folder_id'=>$folder?->id,
              'disk'=>'public',
              'path'=>$path,
              'filename'=>$name,
              'mime_type'=>$file->getMimeType(),
              'size'=>strlen($bytes),
              'width'=>$w,'height'=>$h,
              'uploaded_by'=>$actor?->id,
          ]);
      }
      protected function guard(UploadedFile $file): void {
          $allowed = ['image/jpeg','image/png','image/webp','image/gif','application/pdf'];
          if (!in_array($file->getMimeType(),$allowed)) abort(422,'Unsupported file type');
          if ($file->getSize() > 10*1024*1024) abort(422,'Max 10MB');
          $deny = ['php','phtml','phar','exe','sh','bat','svg'];
          if (in_array(strtolower($file->getClientOriginalExtension()),$deny)) abort(422,'Forbidden extension');
      }
  }
  ```

### Task M8-T03: MediaController + folders

**Files:**
- Create: `app/Http/Controllers/Admin/MediaController.php`, `MediaFolderController.php`
- Create: `resources/views/admin/media/index.blade.php`
- Create: `resources/views/admin/media/_picker.blade.php` (modal partial)

- [ ] **Step 1:** `MediaController@index` returns folder tree + paginated media grid for the current folder (`?folder=`).
- [ ] **Step 2:** `MediaController@store` accepts multipart upload (single or multiple files), iterates calling `MediaUploader::store`, returns JSON `{ items: [...] }` for AJAX consumers.
- [ ] **Step 3:** Folder controller: create/rename/move folder (slug auto-uniqued within parent).
- [ ] **Step 4:** Media picker modal: same UI as full library, with `onSelect` callback that writes the selected URL into a hidden input or to CKEditor.

### Task M8-T04: CKEditor 5 + custom build

**Files:**
- Modify: `package.json`
- Modify: `resources/js/app.js`
- Create: `resources/js/ckeditor-init.js`

- [ ] **Step 1:** Install: `npm install @ckeditor/ckeditor5-build-classic`.
- [ ] **Step 2:** In `app.js` import and provide a global init function:
  ```js
  import ClassicEditor from '@ckeditor/ckeditor5-build-classic';
  window.initCKEditor = (selector) => {
      document.querySelectorAll(selector).forEach(el => {
          ClassicEditor.create(el, {
              toolbar: ['heading','|','bold','italic','underline','link','bulletedList','numberedList','blockQuote','|','fontFamily','fontSize','fontColor','fontBackgroundColor','alignment','|','imageUpload','mediaEmbed','insertTable','|','undo','redo','sourceEditing'],
              fontFamily: { options: ['default','Inter, sans-serif','"Fraunces", serif','Lora, serif','Georgia, serif','system-ui'] },
              mediaEmbed: { previewsInData: true },
          }).catch(console.error);
      });
  };
  ```
- [ ] **Step 3:** In every edit form that uses CKEditor, wrap textarea with `<textarea class="ck-editor"></textarea>` and append:
  ```blade
  @push('scripts')<script>document.addEventListener('DOMContentLoaded', () => initCKEditor('.ck-editor'));</script>@endpush
  ```
- [ ] **Step 4:** Implement "Insert image from library" by overriding the `ImageUpload` adapter: open the media picker modal, on select, insert `<img src>` into the editor with the chosen URL.

---

# Milestone 9 — Contact form + mail

### Task M9-T01: Contact form submission

**Files:**
- Create: `app/Http/Controllers/Site/ContactController.php` (if not from M6)
- Create: `app/Http/Requests/Site/ContactRequest.php`
- Create: `app/Mail/ContactSubmitted.php`
- Create: `resources/views/emails/contact-submitted.blade.php`

- [ ] **Step 1:** `ContactRequest`:
  ```php
  public function rules(): array {
      return [
          'name'=>'required|string|max:120',
          'email'=>'required|email:rfc,dns|max:160',
          'phone'=>'nullable|string|max:32',
          'subject'=>'required|string|max:160',
          'message'=>'required|string|max:5000',
          'website'=>'nullable|size:0',          // honeypot (must be empty)
          'g-recaptcha-response'=>'nullable|string',
      ];
  }
  ```
- [ ] **Step 2:** Controller `submit`:
  ```php
  public function submit(ContactRequest $r) {
      if ($r->filled('website')) return back();   // bot
      $m = ContactMessage::create($r->validated() + [
          'ip'=>$r->ip(),'user_agent'=>$r->userAgent(),
      ]);
      Mail::to(settings('contact.email','admin@church.local'))->queue(new ContactSubmitted($m));
      return back()->with('success','Thanks — we will be in touch.');
  }
  ```
- [ ] **Step 3:** Mailable with markdown view rendering the submission.
- [ ] **Step 4:** Rate limit middleware on the route: `Route::post('/contact', …)->middleware('throttle:contact-form');` and register the limiter in `bootstrap/app.php`:
  ```php
  RateLimiter::for('contact-form', fn($r) => Limit::perHour(5)->by($r->ip()));
  ```

---

# Milestone 10 — Security Hardening

### Task M10-T01: Security headers middleware

**Files:**
- Create: `app/Http/Middleware/SecurityHeaders.php`
- Modify: `bootstrap/app.php`

- [ ] **Step 1:**
  ```php
  public function handle($req, $next) {
      $res = $next($req);
      $res->headers->set('X-Content-Type-Options','nosniff');
      $res->headers->set('X-Frame-Options','DENY');
      $res->headers->set('Referrer-Policy','strict-origin-when-cross-origin');
      $res->headers->set('Permissions-Policy','camera=(), microphone=(), geolocation=()');
      if (app()->isProduction()) {
          $res->headers->set('Strict-Transport-Security','max-age=31536000; includeSubDomains');
      }
      $csp = "default-src 'self'; img-src 'self' data: https:; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; font-src 'self' https://fonts.gstatic.com; script-src 'self' 'unsafe-inline' https://www.google.com https://www.gstatic.com; frame-src 'self' https://www.youtube.com https://player.vimeo.com https://www.google.com;";
          $res->headers->set('Content-Security-Policy', $csp);
      return $res;
  }
  ```
- [ ] **Step 2:** Register in web middleware group via `bootstrap/app.php`.

### Task M10-T02: Rate limiters

**Files:**
- Modify: `bootstrap/app.php`

- [ ] In `withRouting` boot or a service provider:
  ```php
  RateLimiter::for('admin-login', fn($r) => Limit::perMinute(5)->by(strtolower($r->input('email')).'|'.$r->ip()));
  RateLimiter::for('member-login', fn($r) => Limit::perMinute(5)->by(strtolower($r->input('email')).'|'.$r->ip()));
  RateLimiter::for('password-reset', fn($r) => Limit::perHour(3)->by($r->ip()));
  RateLimiter::for('contact-form', fn($r) => Limit::perHour(5)->by($r->ip()));
  ```
  Apply via `->middleware('throttle:admin-login')` on login POST routes.

### Task M10-T03: Failed login lockout

**Files:**
- Create: `app/Listeners/HandleFailedLogin.php`
- Modify: `app/Providers/EventServiceProvider.php` (or auto-discovery)

- [ ] Listener tracks failed attempts per email via cache, locks the email for 15 min after 5 failures, throws `ValidationException` ("Account locked, try again in N min") on subsequent attempts. Wire to `Illuminate\Auth\Events\Failed`.

### Task M10-T04: HTTPS + secure cookies in prod

**Files:**
- Modify: `app/Providers/AppServiceProvider.php`

- [ ] **Step 1:**
  ```php
  public function boot(): void {
      if ($this->app->isProduction()) {
          URL::forceScheme('https');
          config(['session.secure'=>true]);
      }
  }
  ```

### Task M10-T05: Password rules

**Files:**
- Create: `app/Providers/AppServiceProvider.php` (modify)

- [ ] Add to `boot()`:
  ```php
  Password::defaults(fn () =>
      Password::min(12)->mixedCase()->numbers()->symbols()->uncompromised()
  );
  ```
  Reference `Password::defaults()` in all password validation rules.

### Task M10-T06: Force-change default admin password on first login

- [ ] On bootstrap admin record, set `password_change_required = true` (add column via new migration `add_password_change_required_to_users`).
- [ ] After login, middleware `RequirePasswordChange` redirects to a "Set a new password" screen if the flag is set. After successful change, flag is cleared.

### Task M10-T07: Audit-log surfacing

- [ ] Dashboard "Recent activity" section reads from `activity_log` ordered by `created_at desc` limit 10.
- [ ] Users page surfaces `last_login_at` (add column + listener on `Illuminate\Auth\Events\Login`).

---

# Milestone 11 — Tests

### Task M11-T01: Pest base setup

**Files:**
- Modify: `tests/Pest.php`

- [ ] Apply `RefreshDatabase` to Feature tests. Add helper to create an admin user:
  ```php
  function makeAdmin(array $attrs = []): User {
      $u = User::factory()->create(['is_admin'=>true] + $attrs);
      $u->assignRole('Site Admin');
      return $u;
  }
  ```

### Task M11-T02: Auth tests

**Files:**
- Create: `tests/Feature/Auth/AdminLoginTest.php`, `MemberLoginTest.php`, `RateLimitTest.php`, `PasswordResetTest.php`, `SessionIsolationTest.php`

- [ ] **AdminLogin happy path:**
  ```php
  it('logs admin in', function () {
      $u = makeAdmin(['password'=>bcrypt('secret123!')]);
      $this->post('/admin/login', ['email'=>$u->email,'password'=>'secret123!'])
           ->assertRedirect(route('admin.dashboard'));
      expect(auth('admin')->check())->toBeTrue();
  });
  ```
- [ ] **AdminLogin rejects non-admin:**
  ```php
  it('rejects non-admin user at admin login', function () {
      $u = User::factory()->create(['is_admin'=>false,'password'=>bcrypt('secret123!')]);
      $this->post('/admin/login', ['email'=>$u->email,'password'=>'secret123!'])
           ->assertSessionHasErrors('email');
  });
  ```
- [ ] **Rate limit triggers at 6th attempt:**
  ```php
  it('locks login after 5 failures', function () {
      $u = makeAdmin();
      foreach (range(1,5) as $_) {
          $this->post('/admin/login', ['email'=>$u->email,'password'=>'wrong'])
               ->assertSessionHasErrors('email');
      }
      $this->post('/admin/login', ['email'=>$u->email,'password'=>'wrong'])
           ->assertSessionHasErrorsIn('default', ['email'])
           ->assertSessionHas('errors', fn ($e) => str_contains($e->first('email'),'Too many'));
  });
  ```
- [ ] **Member can't access admin routes:**
  ```php
  it('blocks member from /admin', function () {
      $u = User::factory()->create(['is_admin'=>false]);
      $this->actingAs($u, 'web')->get('/admin')->assertStatus(403);
  });
  ```
- [ ] **Session isolation:** log in admin via admin guard, attempt access to `/member` as web guard — should be redirected to `/login`.

### Task M11-T03: CMS CRUD tests

**Files:** one test file per resource in `tests/Feature/Admin/`

- [ ] **Page update:**
  ```php
  it('updates a page with purified body', function () {
      $page = Page::factory()->create(['slug'=>'home']);
      $this->actingAs(makeAdmin(),'admin')
           ->put(route('admin.pages.update',$page), [
               'title'=>'Home','slug'=>'home',
               'body'=>'<script>alert(1)</script><p>Hello</p>',
               'is_published'=>1,
           ])->assertRedirect();
      expect($page->fresh()->body)->not->toContain('<script>')->and->toContain('<p>Hello</p>');
  });
  ```
- [ ] **Permission denied:**
  ```php
  it('denies user without manage-pages', function () {
      $u = User::factory()->create(['is_admin'=>true]);
      $u->assignRole('Event Organizer');     // no manage-pages
      $this->actingAs($u,'admin')->get(route('admin.pages.index'))->assertStatus(403);
  });
  ```
- [ ] Mirror happy-path + permission-denied tests for blog, sermons, events, ministries.

### Task M11-T04: Public site tests

**Files:** `tests/Feature/Site/`

- [ ] Each page returns 200 when seeded; unpublished blog post returns 404; contact form: success path, validation errors, honeypot drops silently, rate limit kicks in at 6th submission.

### Task M11-T05: Security tests

- [ ] **CSRF rejects missing token:** disable CSRF middleware *only for the test app* — actually leave CSRF on and POST without `@csrf` — should return 419.
- [ ] **XSS purification:** stored page body strips `<script>` and `onerror` attributes.
- [ ] **File upload rejects forbidden types:** post `evil.php` as multipart → 422.
- [ ] **Security headers present:** assert `X-Frame-Options`, `Content-Security-Policy`, `X-Content-Type-Options` on a sample GET.

### Task M11-T06: Run + coverage

- [ ] Run `./vendor/bin/pest --coverage --min=70`. Iterate on gaps. Final pass: confirm 100% coverage on `app/Http/Middleware/EnsureIsAdmin.php`, `SecurityHeaders.php`, and `app/Policies/*`.

---

# Milestone 12 — Polish & Handover

### Task M12-T01: Caching for production

**Files:**
- Create: `bin/build-prod.ps1` (script)

- [ ] Document/run for prod:
  ```powershell
  composer install --no-dev --optimize-autoloader
  php artisan config:cache
  php artisan route:cache
  php artisan view:cache
  php artisan event:cache
  npm ci ; npm run build
  ```

### Task M12-T02: README

**Files:**
- Create: `README.md`

- [ ] Sections: Stack, Prerequisites (XAMPP, Node 20+, Composer), Setup (clone, .env, migrate, seed), Run (`php artisan serve` + `npm run dev`), Admin login (read from .env), Phase scope (link to spec + this plan), Production deployment notes (the build-prod script + recommended Apache vhost / .htaccess), Security checklist.

### Task M12-T03: Final manual smoke

- [ ] `php artisan migrate:fresh --seed` clean.
- [ ] Log into `/admin/login`, edit Home page WYSIWYG, save & publish, verify content shows on `/`.
- [ ] Create a blog post, sermon, event, ministry. Confirm each appears on its public index + detail.
- [ ] Upload an image via media library, insert into a page, confirm renders.
- [ ] Change brand color in Settings → verify CSS variables update across site.
- [ ] Edit main menu → confirm public nav reflects changes.
- [ ] Submit contact form → confirm DB row + email log entry.
- [ ] Visit on mobile width via DevTools — every page is usable.
- [ ] Run `./vendor/bin/pest` → all green.

---

# Spec Coverage Self-Check

| Spec section | Plan task(s) |
|---|---|
| §2 In scope (7 public pages) | M3-T03..T09, M6-T05 |
| §2 Admin panel (CRUD for all resources) | M4-T03..T08, M7-T01..T10 |
| §2 Member login placeholder | M5-T04 |
| §2 Site settings (logo, palette, contact, social, footer, SEO, mail) | M2-T12, M6-T01, M6-T02, M7-T09 |
| §2 Media library | M8-T01..T03 |
| §2 Page builder + per-page SEO | M2-T03, M7-T02 |
| §2 Dynamic main nav | M2-T13, M6-T03, M7-T07 |
| §2 Contact form | M9-T01 |
| §3 Stack | M1-T01..T04, M8-T04 |
| §4 Two guards / one User model | M5-T01..T03 |
| §4 Role/permission framework | M2-T10, M7-T01 (middleware applied) |
| §4 Sidebar permission-driven | M4-T01 step 3 + M7-T01 |
| §4 Route protection | M7-T01 (middleware) |
| §4 Auth security (rate limit, bcrypt 12, lockout, signed reset) | M5-T03, M10-T02, M10-T03, M5-T05, M10-T05 |
| §5 DB schema (all tables) | M2-T01..T06 |
| §6 Public UI/UX design system | M1-T04, M3-T02 |
| §6 Page layouts | M3-T03..T09 |
| §6 Responsive | inherent via Tailwind in M3 + smoke test M12-T03 |
| §6 UI-first deliverable (`/preview`) | M3-T01, M3-T10 sign-off |
| §7 Admin design language | M4-T01 |
| §7 Admin screens (Dashboard, Pages, Blog, Sermons, Events, Ministries, Media, Menus, Settings, Messages, Users, Roles) | M4-T03..T08, M7-T02..T10 |
| §7 CKEditor 5 build + sanitization | M8-T04, M7-T02 step 3 |
| §7 Admin UI-first deliverable (`/admin/preview`) | M4-T01, M4-T09 sign-off |
| §8 Input validation (FormRequest) | every CRUD task |
| §8 HTMLPurifier on save | M7-T02 step 2-3 + applied in other resource saves |
| §8 Upload security | M8-T02 step 1 (guard method) |
| §8 Session/cookie hardening | M10-T04, M1-T02 |
| §8 CSRF | Laravel default + M11-T05 test |
| §8 Auth.session middleware | M5-T03 (regenerate on login) |
| §8 Policies for every model | M7-T02 step 5 (pattern repeated per resource) |
| §8 Security headers | M10-T01 |
| §8 HSTS / force HTTPS prod | M10-T01, M10-T04 |
| §8 Honeypot + reCAPTCHA | M9-T01 |
| §8 Audit log | M2-T08, M10-T07 |
| §9 Routes structure (`web.php`, `admin.php`, `member.php`, `auth.php`) | M5-T03, M5-T04, M7-T01 |
| §10 Directory structure | created across milestones |
| §11 Tests (auth, CMS, public, security, coverage ≥ 70%) | M11-T01..T06 |
| §12 Build sequence (UI-first → dynamic) | M3 + M4 sign-off gates, then M5-M7 |
| §13 Acceptance criteria | covered by M12-T03 final smoke + M11 tests |

No gaps detected in coverage.

# Placeholder Scan

- No "TODO"/"TBD"/"implement later" instructions in tasks.
- M2-T09 step 2 refers to "same shape" — but the shape is shown in step 1 example, and step 2 explicitly enumerates each model's relationships. Acceptable.
- M7-T03..T08 say "mirror M7-T02" with the specific differences spelled out — the engineer has the full M7-T02 code as the template earlier in the same document.

# Notes for the implementing engineer

- This plan assumes you can run XAMPP locally (Apache + MySQL) and use VS Code as the editor on Windows.
- Every code block was written for Laravel 12 + PHP 8.3. If on Laravel 11 the only difference of note is middleware registration lives in `app/Http/Kernel.php` (not `bootstrap/app.php`).
- Do not skip the two SIGN-OFF GATES (M3-T10, M4-T09). The user's "design first" requirement is non-negotiable.
- No git commits in this plan (per user instruction). If the user changes their mind, add `git add` + `git commit` after each task naturally.
