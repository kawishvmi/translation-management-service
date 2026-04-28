# Translation Management Service (Laravel)

This project is built for the Laravel Senior Developer coding test.  
It provides a secure and scalable API for managing translations across locales and tags.

## What Has Been Implemented

- Multi-locale translation support (`en`, `fr`, `es`, extensible)
- Translation tagging (`mobile`, `desktop`, `web`)
- API endpoints to create, update, view, search, and export translations
- Search by key, content, locale, and tags
- JSON export endpoint for frontend/Vue consumption
- Token-based authentication using Laravel Sanctum
- Large data seeding command: `translations:seed-large 100000`
- Feature and performance-oriented tests
- OpenAPI file: `openapi.yaml`
- Docker setup: `Dockerfile`, `docker-compose.yml`

## Tech Stack

- PHP 8.2+
- Laravel 12
- Laravel Sanctum
- SQLite (local/dev)
- PHPUnit

## Database Design (Scalable Schema)

Main tables:
- `translation_keys` (`id`, `key`)
- `locales` (`id`, `code`)
- `translation_values` (`id`, `translation_key_id`, `locale_id`, `content`)
- `tags` (`id`, `name`)
- `tag_translation_value` (pivot for many-to-many tags)

Key indexing/scalability choices:
- Unique key constraints on translation key and locale code
- Unique composite index on (`translation_key_id`, `locale_id`)
- Indexed lookup paths for locale and update-time filtering
- Chunked export logic to handle large datasets efficiently

## API Endpoints

### Auth
- `POST /api/auth/token` - generate API token
- `GET /api/auth/token` - helper message for browser/manual checks

### Translations (Protected by Sanctum)
- `GET /api/translations` - list/search translations
- `POST /api/translations` - create or upsert translation
- `GET /api/translations/{translation}` - get single translation
- `PUT /api/translations/{translation}` - update translation
- `GET /api/translations/export` - export JSON for frontend

## Authentication

Use `POST /api/auth/token` with:
- `email`
- `password`
- `device_name`

Default seeded user:
- Email: `admin@example.com`
- Password: `password`

## Local Setup

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed
php artisan serve
