# Emitfy

Emitfy is a professional invoicing platform for Brazilian freelancers and small business owners. Create invoices, manage clients, track payment status, and get paid via Pix — all without leaving the platform.

Built with Laravel 12, Vue 3, and Inertia.js.

## Features

- **Invoicing** — create, edit, and send professional invoices with a public, shareable invoice view
- **Client management** — keep a record of clients and their invoice history
- **Payment tracking** — track invoice status (draft, sent, paid, overdue) and outstanding amounts
- **Pix payments** — collect payments via Pix, Brazil's instant payment system
- **PDF export** — generate downloadable PDF invoices (via DomPDF)
- **Multi-currency support** — BRL, USD, EUR, GBP formatting
- **Authentication** — registration, login, password reset, email verification, and two-factor authentication (via Laravel Fortify)
- **Dashboard** — at-a-glance view of invoices and payment status
- **Bilingual** — Portuguese (PT-BR) and English support
- **Dark/light mode** — full theme support

## Tech Stack

- **Backend**: Laravel 12, Laravel Fortify (auth/2FA), Inertia.js
- **Frontend**: Vue 3, TypeScript, Tailwind CSS 4, Reka UI
- **Database**: SQLite (default for development)
- **PDF Generation**: barryvdh/laravel-dompdf
- **Testing**: Pest PHP

## Getting Started

### Prerequisites

- PHP 8.2+
- Composer
- Node.js & npm

### Installation

```bash
composer install
npm install

cp .env.example .env
php artisan key:generate

php artisan migrate

npm run build
```

### Development

```bash
composer dev              # Starts server, queue worker, and Vite concurrently
composer dev:ssr           # Development with SSR enabled
```

## Testing

```bash
php artisan test                    # Run all tests
php artisan test --filter=TestName  # Run a specific test
```

## Linting & Formatting

```bash
composer lint              # PHP linting with Pint (auto-fix)
composer test:lint         # PHP lint check only
npm run lint                # ESLint for JS/Vue (auto-fix)
npm run format               # Prettier formatting
npm run format:check          # Prettier check only
```

## Build

```bash
npm run build              # Production build
npm run build:ssr           # Production build with SSR
```

## License

MIT
