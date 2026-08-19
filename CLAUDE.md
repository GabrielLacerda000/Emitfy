# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a Laravel 12 + Vue 3 + Inertia.js application built with the Laravel Vue Starter Kit. It uses Laravel Fortify for authentication (including two-factor authentication), Tailwind CSS 4 for styling, and TypeScript throughout the frontend.

## Common Commands

### Development
```bash
composer dev              # Start server, queue worker, and Vite concurrently
composer dev:ssr          # Development with SSR enabled
```

### Testing
```bash
php artisan test                    # Run all tests
php artisan test --filter=TestName  # Run specific test
php artisan test tests/Feature/Auth # Run tests in specific directory
```

### Linting & Formatting
```bash
composer lint             # PHP linting with Pint (auto-fix)
composer test:lint        # PHP lint check only
npm run lint              # ESLint for JS/Vue (auto-fix)
npm run format            # Prettier formatting for resources/
npm run format:check      # Prettier check only
```

### Build
```bash
npm run build             # Production build
npm run build:ssr         # Production build with SSR
```

## Architecture

### Backend (Laravel)
- **Authentication**: Laravel Fortify handles all auth flows including registration, login, password reset, email verification, and 2FA
- **Controllers**: App controllers in `app/Http/Controllers/Settings/` for user settings; Fortify provides auth controllers
- **Concerns**: Shared validation rules in `app/Concerns/` (e.g., `PasswordValidationRules`, `ProfileValidationRules`)
- **Form Requests**: Dedicated request classes in `app/Http/Requests/Settings/` for validation

### Frontend (Vue 3 + Inertia)
- **Pages**: `resources/js/pages/` - Inertia page components resolved by name (e.g., `Dashboard.vue` renders via `Inertia::render('Dashboard')`)
- **Layouts**: `resources/js/layouts/` - App layout (`AppLayout.vue` with sidebar/header variants), Auth layout (card/simple/split variants), Settings layout
- **Components**: `resources/js/components/` - Reusable components including a full shadcn/ui-style component library in `ui/`
- **Composables**: `resources/js/composables/` - Vue composables for appearance, 2FA, URL handling
- **Types**: `resources/js/types/` - TypeScript type definitions for auth, navigation, UI

### Wayfinder Integration
Laravel Wayfinder generates type-safe route helpers. Generated files in `resources/js/actions/` and `resources/js/routes/` provide typed functions for calling Laravel routes from Vue components.

### UI Components
The `resources/js/components/ui/` directory contains a component library based on Reka UI primitives with Tailwind styling. These are not auto-generated and can be customized.

### Database
- SQLite database (default for development)
- Standard Laravel auth tables plus Fortify 2FA columns on users table

## Testing
- Uses Pest PHP testing framework
- Feature tests use `RefreshDatabase` trait automatically (configured in `tests/Pest.php`)
- Auth tests cover registration, login, password reset, email verification, 2FA challenge
- Settings tests cover profile updates, password changes, 2FA setup