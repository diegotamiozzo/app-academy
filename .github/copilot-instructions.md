# Copilot instructions for this repository

## Project snapshot

This is a Laravel 12 starter app built around Livewire + Volt and Vite. The primary flow is a standard authenticated app shell: home page, dashboard, auth screens, and user settings. The app uses Eloquent for the `User` model and Blade templates for layout, while interactive pages are typically defined as Volt components instead of classic controller-heavy MVC patterns.

## Build, test, and lint commands

### Install dependencies

```bash
composer install
npm install
```

### Local environment setup

```bash
cp .env.example .env
php artisan key:generate
php artisan migrate
```

If `database/database.sqlite` does not exist yet, create it first:

```bash
touch database/database.sqlite
```

### Run the app locally

The most complete local startup for this project is:

```bash
composer run dev
```

This starts the Laravel app, queue listener, log watcher, and Vite dev server together.

If you need to run pieces individually:

```bash
php artisan serve
npm run dev
```

### Frontend build

```bash
npm run build
```

### Run the test suite

```bash
php artisan test --compact
```

Run a single test file:

```bash
./vendor/bin/pest tests/Feature/<Name>.php
```

Run a single test by filter:

```bash
./vendor/bin/pest tests/Feature/<Name>.php --filter="<test name>"
```

### PHP formatting / linting

This project uses Pint for formatting:

```bash
./vendor/bin/pint --dirty --format agent
```

To run the formatter across the full project:

```bash
./vendor/bin/pint --format agent
```

## High-level architecture

- `bootstrap/app.php` is the Laravel 12 app bootstrap. It wires routing and middleware for the app.
- `routes/web.php` defines the app shell routes: the landing page, the protected dashboard, and settings pages.
- `routes/auth.php` defines the regular auth and password-reset flow using `Livewire\Volt\Volt::route(...)` for the public flows and dedicated auth actions.
- `app/Models/User.php` is the base Eloquent model for authentication and user details.
- `app/Livewire/Actions/Logout.php` is the action class used for logout; most app-level interactive tasks are built as Livewire/Volt-based actions and views instead of custom controller classes.
- `resources/views/` contains the Blade templates and layout components. `x-layouts.app` is the app shell used by the dashboard and settings screens.
- `resources/views/livewire/` and `resources/views/components/` contain reusable UI pieces and page components. Check these first before creating a new UI pattern.
- `vite.config.js` and the `package.json` scripts control the frontend build pipeline; the app is expected to work with Vite rather than a separate custom asset pipeline.
- The default database is SQLite (`database/database.sqlite`), and the tests use `RefreshDatabase` via `tests/Pest.php`.

## Key conventions in this repo

- Follow Laravel 12 conventions instead of older Laravel app structure patterns. There is no legacy `app/Http/Kernel.php` or `app/Console/Kernel.php` in this starter.
- Prefer `Volt` single-file Livewire components for UI logic that needs interactivity; match the existing file and route conventions before introducing a new pattern.
- For protected screens, keep route guards in `routes/web.php` and `routes/auth.php` and use the existing `auth` / `verified` middleware rather than duplicating access checks in views.
- Use named routes for navigation and redirects where the app already exposes them.
- Keep UI/layout composition aligned with the existing Blade component structure (`x-layouts.app`, `x-placeholder-pattern`, etc.), rather than introducing a different component system.
- The repo is configured for Pest tests, not PHPUnit-only style. Feature tests live under `tests/Feature` and inherit the `RefreshDatabase` trait from `tests/Pest.php`.
- If a change affects the frontend, validate with `npm run build`; if it affects app logic or route behavior, validate the relevant Pest tests instead of relying only on static inspection.

## Practical guidance for future sessions

- Read the route files and the relevant Blade/Volt component before adding or changing behavior; the app’s structure is intentionally compact and route-driven.
- Treat `routes/` and `resources/views/` as the main source of app behavior, with models only where persistence or auth logic is needed.
- Verify asset issues after frontend changes with Vite (`npm run build`) before assuming the app is ready.
- Keep changes aligned with the starter-kit conventions rather than introducing broader Laravel patterns that this repo does not use.
