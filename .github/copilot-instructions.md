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

If using SQLite locally and `database/database.sqlite` does not exist yet:

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

Format only changed PHP files:

```bash
./vendor/bin/pint --dirty --format agent
```

Format the whole project when needed:

```bash
./vendor/bin/pint --format agent
```

## High-level architecture

- `routes/web.php` and `routes/auth.php` are the primary entry points for app behavior. The shell is route-driven: public landing page, authenticated dashboard, and settings pages are defined here.
- `Volt::route(...)` is used for auth and settings screens, so new interactive flows should usually be implemented as Livewire Volt components instead of controller actions.
- `resources/views/` is the main source of UI and behavior: `welcome.blade.php`, `dashboard.blade.php`, `resources/views/components/`, and `resources/views/livewire/` hold the app's reusable UI patterns.
- `app/Models/User.php` handles auth data and user helpers; it is not the main place for route or screen logic.
- `app/Livewire/Actions/Logout.php` is the custom logout action used by the auth routes.
- `bootstrap/app.php` and the streamlined Laravel 12 structure are the conventions here; there is no legacy `app/Http/Kernel.php` or `app/Console/Kernel.php`.
- `vite.config.js`, `package.json`, and the Tailwind/Flux setup define the front-end pipeline; Vite is the expected asset build system.
- The default local database is SQLite (`database/database.sqlite`), which is the typical dev setup for this starter kit.

## Key conventions

- Follow the Laravel 12 starter-kit structure and existing app patterns before introducing new abstractions.
- Prefer Volt single-file Livewire components for interactive screens and features; match the surrounding component conventions in `resources/views/components` and `resources/views/livewire` before creating new UI structure.
- Keep route guards and authentication logic in `routes/web.php` and `routes/auth.php` instead of reimplementing checks in Blade or component logic.
- Use named routes and redirects (`route(...)`, `->name(...)`) consistently when navigating between sections of the app.
- Reuse the existing dashboard/settings shell, auth header, and component patterns instead of creating a separate UI system.
- Treat `routes/` and `resources/views/` as the primary source of behavior; models are mainly for persistence and auth support, not page logic.
- If the change affects front-end assets or styles, validate with `npm run build`. If it affects auth, authorization, or app flow, validate the relevant Pest tests.
- This repository includes Laravel Boost/AI guidance and skills; keep changes aligned with the starter-kit conventions already in place.
