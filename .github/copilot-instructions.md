# Copilot instructions for this repository

## Project snapshot

This repository is a Laravel 12 web app for an online academy built from the official Livewire starter kit. It combines Laravel, Livewire Volt, Flux UI, Tailwind, and Vite, with a route-driven shell for authenticated pages, settings, and auth flows. The project is not a large MVC app with controllers for every feature; most interactive pages are single-file Volt components and Blade views.

## Build, test, and lint commands

### Install dependencies

```bash
composer install
npm install
```

### Local setup

```bash
cp .env.example .env
php artisan key:generate
php artisan migrate
```

If `database/database.sqlite` does not exist yet:

```bash
touch database/database.sqlite
```

### Run locally

The usual full local startup is:

```bash
composer run dev
```

This starts the Laravel app, queue worker, log watcher, and Vite dev server together. You can also run pieces separately:

```bash
php artisan serve
npm run dev
```

### Frontend build

```bash
npm run build
```

### Test commands

Run the full suite:

```bash
php artisan test --compact
```

Run a single file:

```bash
./vendor/bin/pest tests/Feature/<Name>.php
```

Run a single test by filter:

```bash
./vendor/bin/pest tests/Feature/<Name>.php --filter="<test name>"
```

### Formatting / linting

```bash
./vendor/bin/pint --dirty --format agent
```

To format the whole project instead of only dirty files:

```bash
./vendor/bin/pint --format agent
```

## High-level architecture

- `bootstrap/app.php` is the Laravel 12 application bootstrap. It wires routing and middleware with the streamlined Laravel 11+ app structure.
- `routes/web.php` defines the public landing page, the protected dashboard, and the settings routes for profile/password/appearance. The app shell is intentionally route-focused rather than controller-heavy.
- `routes/auth.php` defines guest auth and password reset flows using `Livewire\Volt\Volt::route(...)`, plus verification and logout handling.
- `app/Models/User.php` is the Eloquent user model. It handles auth data, password hashing, and the user initials helper used in the UI.
- `app/Livewire/Actions/Logout.php` is the action class for the `logout` route and is the main custom action outside the Volt views.
- `resources/views/` is the main source of behavior for pages and UI. `welcome.blade.php`, `dashboard.blade.php`, and the `components/` tree contain the app shell and reusable Blade blocks.
- `resources/views/livewire/` and `resources/views/components/` are the first places to look for UI patterns, especially auth/settings flows and reusable components.
- `vite.config.js` and `package.json` define the front-end toolchain; Vite is the expected asset pipeline, not a custom build step.
- `tests/Pest.php` configures Pest to use `RefreshDatabase` for feature tests. The default local database is SQLite (`database/database.sqlite`).

## Key conventions

- This project follows Laravel 12 conventions rather than older app structure patterns. There is no legacy `app/Http/Kernel.php` or `app/Console/Kernel.php`.
- Prefer `Volt` single-file Livewire components for interactive screens and features; match surrounding patterns in `resources/views/components` and `resources/views/livewire` before introducing new UI structure.
- Keep auth and route guards in `routes/web.php` and `routes/auth.php` instead of re-implementing access checks in Blade or component logic.
- Use named routes and redirects (`route(...)`, `->name(...)`) consistently when navigating between areas of the app.
- Reuse the existing app shell and component conventions (dashboard/settings layout, auth header, logo, placeholder patterns) instead of creating a different UI system.
- Treat `routes/` and `resources/views/` as the primary source of app behavior; models are used mainly for persistence and auth, not as the main place for screen logic.
- If a change impacts front-end assets, validate with `npm run build`. If it impacts auth, authorization, or app flow, validate the relevant Pest tests instead of relying only on static inspection.
- The repo is configured for Laravel Boost and includes AI helper files and skills for Copilot sessions; keep changes aligned with the starter-kit conventions already in place.
