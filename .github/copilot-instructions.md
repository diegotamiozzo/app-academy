# Copilot instructions for this repository

## Project snapshot

This repository is a Laravel 12 + Livewire 4 application for an online academy, built from the official Livewire starter kit. It uses Laravel's native auth, Volt single-file components, Flux UI, Tailwind, and Vite. Most app behavior is route-driven and lives in Blade/Volt files rather than a controller-heavy MVC structure.

## Build, test, and lint commands

### Initial setup

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate
```

If using SQLite locally and `database/database.sqlite` does not exist yet, create it before running migrations:

```bash
touch database/database.sqlite
```

### Run the app locally

Use the project defaults for a full local stack:

```bash
composer run dev
```

This starts the Laravel app, queue worker, log watcher, and Vite dev server together. If needed, run the pieces separately:

```bash
php artisan serve
npm run dev
```

### Frontend build

```bash
npm run build
```

### Tests

Run the full Pest suite:

```bash
php artisan test
```

Run one test file or a filtered test:

```bash
php artisan test tests/Feature/AccessControlTest.php
php artisan test tests/Feature/AccessControlTest.php --filter="redeems access codes"
```

### Formatting / linting

Format PHP with Pint:

```bash
./vendor/bin/pint
```

CI also builds frontend assets with `npm run build`; run it after changing frontend assets or styles. The test workflow runs `./vendor/bin/pest`, and the quality workflow runs Pint.

## High-level architecture

- `routes/web.php` defines the landing page and role-specific dashboards; `routes/auth.php` maps authentication endpoints. Most page behavior is route-driven, with dashboard data passed into Blade views.
- Authentication and settings screens use Volt single-file components under `resources/views/livewire/auth/` and `resources/views/livewire/settings/`. Their PHP component class and Blade template live in the same file. Shared Blade components and layouts are under `resources/views/components/`.
- The academy domain is organized around companies, license purchases, access codes, and users. `User` stores a `Role` enum value and belongs to a company; access-code redemption assigns the user's company and role.
- Laravel 12 application setup is in `bootstrap/app.php` and `bootstrap/providers.php`. Route middleware aliases are registered in `bootstrap/app.php`; there is no `app/Http/Kernel.php`.
- Vite builds the frontend from `resources/css/app.css` and the configured JavaScript entry points. Tailwind and Flux styles are wired through the CSS entry point.
- SQLite is the configured default for local setup and the in-memory database used by Pest tests.

## Key conventions

- Use the existing route-driven and Volt patterns for pages. Keep named route names and redirects consistent with the role dashboards and settings routes.
- The `dashboard` route redirects according to `App\Enums\Role`; protect role-specific pages with the `role` middleware alias and do not rely only on hiding links in the UI.
- Keep access-code redemption logic centralized in `App\Models\AccessCode::redeemForUser()`. It updates the user and code within a transaction and locks the selected code; changes to signup or redemption should preserve that atomic flow.
- Reuse the existing application/auth layouts, Blade components, and Flux UI components. For styles, follow the Tailwind CSS setup in `resources/css/app.css`.
- Check the relevant project guidance under `.github/skills/` before changing Laravel, Volt, Flux, Tailwind, or Pest code. Confirm package versions from `composer.json` or `package.json` when relying on version-specific APIs.
- For PHP behavior changes, run the relevant Pest tests; for frontend changes, run `npm run build`. Run `./vendor/bin/pint` for PHP formatting.

## Current feature plan

`PLANO-CONTEUDO-INSTRUTOR.md` is the design source for the instructor course-content feature. The MVP for Phases 1–4 has been implemented. The confirmed decisions are: course material is available immediately; every module requires Slides; instructors edit only courses they created; access is granted only to same-company users; all modules are open; progress tracking is deferred; and Google Drive uses the MVP link/embed model, not Phase 6 service-account protection.

For this feature, persist extracted Google Slides, YouTube, and Drive IDs rather than submitted URLs. `App\Services\CourseLinkParser` validates domains and IDs, then constructs embed URLs. `Course` and `CourseModule` use the `BelongsToCompany` global scope; the admin bypasses it, and unauthenticated CLI, seeder, and job queries must explicitly use `withoutGlobalScope(CompanyScope::class)` when they need cross-company data. Enforce course visibility through published status, company membership, explicit `course_user` access, and `CoursePolicy`; never trust submitted company or role values. Embedded public content uses iframe sandboxing to block pop-ups and top-level navigation. PDF downloads go through `CourseMaterialDownloadController`, which checks authorization and the course download flag, then streams the PDF through the platform. These controls keep normal playback and downloads within the app, but do not make public provider files private; anyone who obtains a public ID can still open it directly.
