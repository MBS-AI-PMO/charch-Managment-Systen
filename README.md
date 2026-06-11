# Church CMS

A Laravel-based Church Management System with a public website, an admin CMS, role-based access, media library, contact form, and security hardening. Phase 1 build — production-ready foundation for a multi-phase rollout.

## Stack

| Layer | Technology |
| --- | --- |
| Backend | Laravel 12, PHP 8.2+ |
| Database | MySQL 8 (utf8mb4_unicode_ci) |
| Frontend | Blade, Tailwind CSS v3, Alpine.js, Vite |
| Auth & ACL | Laravel auth (2 guards: `admin`, `web`) + Spatie Laravel Permission |
| Editor | CKEditor 5 (classic), sanitised via `mews/purifier` |
| Media | Intervention Image (image resize/thumb), custom Media + Folder models |
| Email | Symfony Mailer (SMTP via `MAIL_*`) |
| Testing | Pest 3 + Pest-Laravel plugin (113 passing + 1 skipped, 300 assertions) |

## Prerequisites

- XAMPP (Apache + MySQL) — or any LAMP/WAMP stack
- PHP **8.2+**
- Composer 2.x
- Node **20+** and npm
- (Optional) `xdebug` or `pcov` for test coverage

## Local setup

> All commands shown for Windows PowerShell. On macOS/Linux substitute paths accordingly.

1. **Copy the project** to `d:/XAMPP/htdocs/Chruch Managment System` (or your XAMPP htdocs equivalent).

2. **Add XAMPP binaries to PATH** for the current shell:

    ```powershell
    $env:PATH = "D:\XAMPP\php;D:\XAMPP\mysql\bin;$env:PATH"
    ```

3. **Install PHP deps:**

    ```powershell
    composer install
    ```

4. **Create `.env`:**

    ```powershell
    Copy-Item .env.example .env
    ```

5. **Edit `.env`** — at minimum:

    ```ini
    DB_CONNECTION=mysql
    DB_DATABASE=church_cms
    DB_USERNAME=root
    DB_PASSWORD=

    ADMIN_EMAIL="admin@church.local"
    ADMIN_PASSWORD="ChangeMe!2026"

    MAIL_MAILER=smtp
    MAIL_HOST=...
    MAIL_PORT=...
    MAIL_USERNAME=...
    MAIL_PASSWORD=...
    ```

6. **Create the database:**

    ```powershell
    mysql -u root -e "CREATE DATABASE church_cms CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
    mysql -u root -e "CREATE DATABASE church_cms_test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
    ```

7. **Generate app key:**

    ```powershell
    php artisan key:generate
    ```

8. **Migrate + seed:**

    ```powershell
    php artisan migrate --seed
    ```

9. **Link public storage** (run elevated PowerShell, or use the junction fallback):

    ```powershell
    php artisan storage:link
    # If that fails (NTFS permissions), in elevated PowerShell:
    cmd /c mklink /J "public\storage" "storage\app\public"
    ```

10. **Build frontend assets:**

    ```powershell
    npm install
    npm run build      # production bundle
    # or
    npm run dev        # Vite HMR while developing
    ```

11. **Run the dev server:**

    ```powershell
    php artisan serve --port=8000
    ```

## Default credentials

The bootstrap admin user is created by the `SiteAdminSeeder` from `.env` (`ADMIN_EMAIL` / `ADMIN_PASSWORD`). The user is flagged `password_change_required=true`, so the **first admin login forces a password change** before any admin route becomes accessible.

## URLs

| Surface | URL |
| --- | --- |
| Public site | `http://localhost:8000/` |
| Admin login | `http://localhost:8000/admin/login` |
| Member login (Phase 1 stub) | `http://localhost:8000/login` |
| Public design previews (local env only) | `/preview/*` |
| Admin design previews (local env only) | `/admin/preview/*` |

## Phase scope

**Phase 1 (this build)** ships:

- Public website with **7 dynamic pages** (home, about, sermons, events, ministries, blog, contact)
- Full **admin CMS** with 15 index screens and CRUD for pages, blog, sermons, events, ministries, media, menus, users, roles, settings
- **Two-guard auth** (`admin` + `web`), Spatie roles/permissions, force-password-change, password reset, login lockout
- **Media library** with folders + CKEditor 5 integration sanitised by HTMLPurifier
- **Contact form** with mail delivery + message inbox in admin
- **Security hardening** — CSP / `X-Frame-Options` / `Referrer-Policy` headers, throttling, bcrypt cost 12, strong password rules
- **74 Pest tests** (181 assertions) covering public pages, admin smoke, auth flows, content visibility

**Phase 2 (shipped)** adds:

- **Member self-signup** at `/register` with email verification (signed URL → `/member`)
- **Member portal** at `/member/*`:
    - Dashboard, Profile editing + password change
    - Prayer Requests: submit own (public / private / anonymous) and browse community board with an "I'm praying" toggle
    - Knock for Help — pastoral care requests routed to staff inbox + email
    - Community Feed — react to admin posts (heart / amen / pray)
    - Give — public `/donate` page with bank-transfer instructions (no Stripe)
- **Three new admin modules** with role-gated sidebars:
    - Prayer Requests (moderation, status, assign to pastor)
    - Knock for Help (triage, assign, resolve)
    - Community Feed (compose, schedule, broadcast "Send to members")
- **Sub-admin roles wired**:
    - **Site Admin** — sees everything
    - **Prayer Organizer** — sees the 3 new modules + Members
    - **Event Organizer** — sees Events + Ministries (+ Phase 3 stubs)
- **Public `/donate`** page editable via Pages CMS (no payment provider; bank-transfer model)
- **Phase 3 stub pages** for Attendance + Reminders (read-only placeholders behind role gates)

**Out of scope (deferred):**

- Phase 3 — online giving (Stripe), event registration, attendance tracking, automated reminders, reports
- Android companion app — deferred indefinitely

## Modules

| # | Module | Phase 1 | Phase 2/3 |
| --- | --- | --- | --- |
| 1 | Public Website | Live | — |
| 2 | Sermons | Live | — |
| 3 | Events | Live (display only) | Registration → Phase 3 |
| 4 | Ministries | Live | — |
| 5 | Blog | Live | — |
| 6 | Contact & Messages | Live | — |
| 7 | Media Library | Live | — |
| 8 | Users / Roles / Permissions | Live (Site Admin role) | Members + sub-admin roles ✅ Phase 2 |
| 9 | Site Settings & Menus | Live | — |
| 10 | Member Portal | — | ✅ Phase 2 |
| 11 | Prayer Requests | — | ✅ Phase 2 |
| 12 | Knock for Help | — | ✅ Phase 2 |
| 13 | Community Feed | — | ✅ Phase 2 |
| 14 | Donate (bank transfer) | — | ✅ Phase 2 |
| 15 | Attendance | — | Stub (Phase 3) |
| 16 | Reminders | — | Stub (Phase 3) |

## Operational notes

- **Queue worker required** for Community Feed "Send to members" broadcasts. `QUEUE_CONNECTION=database` is the default; in production run `php artisan queue:work` (or a Supervisor-managed worker) so queued member notifications are dispatched.
- **First-time admin login** — accounts created by the seeder are flagged `password_change_required=true`. The very first login forces a password change before any admin route is reachable.
- **Member email verification** uses `MAIL_MAILER=log` in development — verification links land in `storage/logs/laravel.log`. Switch to SMTP (`MAIL_MAILER=smtp` + real credentials) for production.

## Project structure

```
app/
  Http/Controllers/
    Site/         (public website)
    Admin/        (CMS)
    Auth/         (admin + member auth, force-pw, reset)
  Models/         (Page, BlogPost, Sermon, Event, Ministry, Media, etc.)
  Mail/, Policies/, Providers/
routes/
  web.php         (site + admin + auth + preview)
resources/views/
  site/           (public Blade)
  admin/          (admin Blade)
  auth/           (login/reset/force-pw)
  layouts/        (site + admin layouts)
  components/     (reusable Blade components)
  emails/         (mailables)
  preview/        (design-time previews — local env only)
tests/
  Feature/        (Pest tests: Site, Admin, Auth, Security)
bin/
  build-prod.ps1  (production build script)
docs/
  superpowers/    (spec + plan)
```

## Testing

```powershell
./vendor/bin/pest
# or
php artisan test
```

- Test DB is `church_cms_test` (configured in `phpunit.xml`).
- Tests use `RefreshDatabase`, so they're isolated from local seeded data.
- Coverage requires Xdebug or PCOV: `php artisan test --coverage`.

## Production deployment

1. Provision LAMP host (Linux preferred). Apache or Nginx; PHP 8.2+; MySQL 8; Node 20+.
2. Set Apache **document root → `/public`** (or equivalent Nginx `root`).
3. Configure `.env` with **production values**:
    - `APP_ENV=production`
    - `APP_DEBUG=false`
    - Valid `APP_KEY`
    - Real `MAIL_*` SMTP credentials
    - Real `RECAPTCHA_*` keys (when reCAPTCHA is added)
4. Run the production build script:

    ```powershell
    ./bin/build-prod.ps1
    ```

    This installs `--no-dev` Composer deps, caches config/routes/views/events, builds the Vite bundle, and creates the storage symlink.

## Security checklist

- Bcrypt **cost 12** (configured in `config/hashing.php`)
- Strong password rule: **12+ chars, mixed case, numbers, symbols**
- Failed-login **lockout: 5 attempts → 15 min**
- **Force-change** on first admin login
- Throttle limiters on **admin login, member login, password reset, contact form**
- **CSRF** on all `POST/PUT/PATCH/DELETE`
- **HTMLPurifier** on all CKEditor output (prevents stored XSS)
- Security headers via middleware:
    - `X-Frame-Options: DENY`
    - `Content-Security-Policy` (default-src self + Vite assets)
    - `X-Content-Type-Options: nosniff`
    - `Referrer-Policy: strict-origin-when-cross-origin`
- All admin routes wrapped in `auth:admin` + `force-password-change` middleware

## References

- Spec: [`docs/superpowers/specs/2026-05-30-church-cms-phase-1-design.md`](docs/superpowers/specs/2026-05-30-church-cms-phase-1-design.md)
- Plan: [`docs/superpowers/plans/2026-05-30-church-cms-phase-1.md`](docs/superpowers/plans/2026-05-30-church-cms-phase-1.md)

## License

**Proprietary — internal use only.** Not licensed for redistribution.

## Credits

Built with [Claude Code](https://claude.com/claude-code) (Anthropic).
