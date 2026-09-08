# Laravel Admin Panel

A Laravel 12 contact-management application for administering companies and their employees.

## Requirements

- PHP 8.2 or newer
- Composer
- Node.js and npm
- SQLite (default) or another Laravel-supported database

## Local setup

1. Install the PHP dependencies:

   ```bash
   composer install
   ```

2. Create the local environment file from the committed template:

   ```bash
   copy .env.example .env
   ```

   On macOS or Linux, use `cp .env.example .env` instead.

3. Generate the application key:

   ```bash
   php artisan key:generate
   ```

4. Create `database/database.sqlite` when using the default SQLite configuration, then run the migrations and seeders:

   ```bash
   php artisan migrate --seed
   ```

5. Install and build the frontend assets:

   ```bash
   npm install
   npm run build
   ```

6. Start the application:

   ```bash
   php artisan serve
   ```

## Environment security

The local `.env` file is intentionally ignored by Git because it can contain credentials and application secrets. Commit `.env.example` only, keeping its values safe for documentation and local bootstrapping. Never add a real `APP_KEY`, database password, mail credential, or third-party secret to `.env.example`.

If using MySQL instead of SQLite, change `DB_CONNECTION` in the local `.env` file to `mysql` and set the provided `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, and `DB_PASSWORD` placeholders locally.

## Checks

```bash
php artisan test
npm run build
```
