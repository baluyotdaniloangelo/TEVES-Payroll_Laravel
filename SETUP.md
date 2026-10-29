# Teves Payroll local setup

The application uses PHP 8.2 or later and Laravel 12. Keep `composer.lock` in Git and use `composer install` after cloning so each environment installs the same tested versions.

## Install the application

Run these commands from the payroll project folder:

```sh
composer install
```

For a fresh checkout, copy `.env.example` to `.env`, then run:

```sh
php artisan key:generate
```

Keep an existing `.env` and application key when updating an existing installation.

## Connect the existing payroll database

Set the database connection values in `.env` to the intended local payroll database. The historical payroll tables were maintained with SQL scripts in `Database Changes`; the migrations folder alone does not recreate the entire payroll schema.

After the correct payroll database has been imported and its connection is configured, run:

```sh
php artisan migrate
```

The compatibility migration adds missing `event` and `batch_uuid` metadata columns to an existing activity log table. It preserves existing audit rows and leaves these metadata columns in place on rollback. It does not create the historical payroll tables.

## Verify and run

```sh
composer check-platform-reqs
composer audit
php artisan test
php artisan serve
```

The automated compatibility checks use an in-memory SQLite database. They cover the login page, protected-page redirects, PDF generation, CSV parsing, spreadsheet writing and reading, and model activity logging. They do not certify payroll calculations or validate the imported business data.

## Compatibility changes

- Updated the framework, PDF, DataTables, spreadsheet, Carbon, Sanctum and development packages to mutually compatible versions for PHP 8.2.
- Replaced the legacy proxy, CORS and maintenance middleware with framework middleware.
- Preserved the existing `PDF` alias and the model-specific activity log names and tracked attributes.
- Removed unused Charts, Laravel Excel and MySQL index wrapper dependencies. The application uses PhpSpreadsheet and League CSV directly.
- Corrected the Holiday controller and Welcome mail class names. Archived model copies remain in place and are excluded from optimized class mapping.
- Removed two unused route registrations that referenced missing allowance-selection and user-update handlers. Allowance options remain in the existing form, and the existing create-or-update user endpoint remains available.
- Security advisory blocking remains enabled.
