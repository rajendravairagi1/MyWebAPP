# BusinessFlow

Laravel app for the BusinessFlow SaaS product (see the PRD-derived build plan for
full context). This lives inside the `MyWebAPP` repo as its own subfolder,
separate from the existing Next.js agency site — different framework, different
deploy step, same repo.

## What's built so far (Foundation phase)

- Laravel 13 + Breeze (Blade stack) — registration, login, email verification,
  profile screens.
- Laravel Sanctum installed (`routes/api.php`) — ready for the future Android
  app to authenticate against the same backend.
- **Multi-tenant core**:
  - `businesses` + `business_user` tables (a business is a tenant; a user can
    belong to more than one business, with a role per business).
  - `App\Support\Tenant` — holds the active `business_id` for the current
    request only.
  - `App\Http\Middleware\IdentifyTenant` — resolves the active business from
    the session (never from client input), and redirects a user with no
    business yet to onboarding.
  - `App\Models\Concerns\BelongsToTenant` — trait every future tenant-owned
    model (Customer, Lead, Quotation, Invoice, ...) will use. It adds a global
    scope that filters every query to the active tenant, and auto-fills
    `business_id` on create.
- Onboarding flow (`/onboarding/business`) — business name, type, country,
  currency, timezone → creates the tenant and lands on the dashboard.

## Local development (this sandbox / any machine with PHP 8.2+ and Composer)

```bash
cd businessflow
cp .env.example .env   # already done in this session
composer install
php artisan key:generate
touch database/database.sqlite   # local dev uses sqlite; production uses MySQL
php artisan migrate
npm install && npm run build     # or `npm run dev` while working on views
php artisan serve
```

## Deploying to shared/cPanel hosting

1. In cPanel, create a MySQL database + user, and grant the user full
   privileges on that database.
2. On the server, set in `.env`:
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=<your_db_name>
   DB_USERNAME=<your_db_user>
   DB_PASSWORD=<your_db_password>
   ```
3. Point the domain/subdomain's document root at `businessflow/public`.
4. Via SSH (Terminal app in cPanel, if available): `composer install --no-dev`,
   `php artisan migrate --force`, `php artisan key:generate`,
   `php artisan storage:link`.
5. Add a cron job (cPanel → Cron Jobs) running every minute:
   ```
   * * * * * cd /home/USER/businessflow && php artisan schedule:run >> /dev/null 2>&1
   ```
   This drives queued jobs and scheduled tasks (reminders, overdue-invoice
   notifications) — no Redis needed.

If the hosting panel has no SSH/Composer access, the fallback is a
Softaculous Laravel installer or uploading a pre-built `vendor/` folder — ask
before doing this the first time, since it changes the deploy process.

## Next up

Core CRM + sales documents (customers, leads, products/services, quotations,
invoices, payments) — same `BelongsToTenant` pattern, following the build
plan's module order.
