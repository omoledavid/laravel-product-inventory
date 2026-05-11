# Product Inventory System

A small Laravel application that manages a product catalog and applies discount rules through a mini pricing engine. Built as an interview assessment.

## Features

- **Storefront** at `/` — browse products, search by title/description, filter by category, paginate 12 per page.
- **Product page** — shows title, description, category, original price, any active discount, and the final price.
- **Pricing engine** — service that applies a per-product discount (percentage or fixed amount) and returns a `PriceBreakdown` value object. Percentages are floored to the nearest cent; fixed amounts are capped at the product's price.
- **Admin panel** at `/admin` — full CRUD for products (including discount fields) and categories. Form-request validation, flash messages, pagination.
- **Tests** — 54 Pest feature tests covering models, the pricing engine, the storefront, search/filter/pagination, and admin CRUD.

## Tech stack

- PHP 8.3
- Laravel 13
- SQLite (default — file at `database/database.sqlite`)
- Tailwind CSS v4 via Vite
- Pest 4 for testing

## Project layout

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── ProductController.php          # Storefront index + show
│   │   └── Admin/
│   │       ├── ProductController.php      # Admin CRUD
│   │       └── CategoryController.php
│   └── Requests/Admin/
│       ├── ProductRequest.php
│       └── CategoryRequest.php
├── Models/
│   ├── Product.php                        # title, slug, price_cents, discount_type, ...
│   └── Category.php
├── Services/PricingEngine.php             # Singleton, computes PriceBreakdown
└── Support/PriceBreakdown.php             # Readonly DTO with formatted accessors
database/
├── migrations/                            # categories, products (+ discount cols)
├── factories/                             # withPercentDiscount / withFixedDiscount states
└── seeders/DatabaseSeeder.php             # 4 categories, ~45 products
resources/views/
├── layouts/{app,admin}.blade.php
├── products/{index,show}.blade.php        # Storefront
└── admin/{products,categories}/...
tests/Feature/                             # Pest tests
```

## Data model

A `Product` belongs to a `Category` (nullable; products survive category deletion).
Discount lives on the product:

| Column | Type | Notes |
| --- | --- | --- |
| `discount_type` | `string?` | `'percent'`, `'fixed'`, or `null` |
| `discount_percent` | `decimal(5,2)?` | Used when type is `percent` (0–100) |
| `discount_amount_cents` | `unsigned int?` | Used when type is `fixed` |

Prices are stored as integer cents (`price_cents`) to avoid floating-point drift.

## Setup

Requires PHP 8.3+, Composer, and Node 20+.

```bash
git clone <repo>
cd laravel-product-inventory

composer install
npm install

cp .env.example .env
php artisan key:generate

# SQLite is the default — create the database file
php artisan migrate:fresh --seed

# Build frontend assets
npm run build
```

## Running locally

```bash
# Terminal 1: serve the app
php artisan serve

# Terminal 2 (optional, for hot-reloading assets during development):
npm run dev
```

Then visit:
- Storefront: <http://127.0.0.1:8000>
- Admin: <http://127.0.0.1:8000/admin>

## Running tests

```bash
./vendor/bin/pest
```

Tests run against an in-memory SQLite database (configured in `phpunit.xml`) and don't touch your dev DB.

## Useful artisan commands

```bash
php artisan migrate:fresh --seed   # Rebuild DB with seed data
php artisan route:list             # See all routes
php artisan tinker                 # REPL — try Product::with('category')->get()
```

## Configuration

| Env var | Default | Purpose |
| --- | --- | --- |
| `APP_CURRENCY_SYMBOL` | `$` | Symbol used by `PriceBreakdown::format()` |
| `DB_CONNECTION` | `sqlite` | Database driver |
