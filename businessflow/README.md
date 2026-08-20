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

## Deploying to shared hosting with no SSH access

No terminal, no Composer on the server — the same pattern most self-hosted
PHP scripts (WordPress, off-the-shelf ERPs, etc.) use: build the package
somewhere that *does* have Composer (this sandbox, or any dev machine), then
upload it whole and finish setup through a one-time web installer.

1. **Set an install token before packaging.** In `.env`, set `INSTALL_TOKEN`
   to a private random string (`php -r "echo bin2hex(random_bytes(16));"`).
   Without the matching `?token=` in the URL, `/install` returns 403 — this
   is what stops a stranger from reconfiguring the database before you do.
2. **Build the deployable package** (needs Composer — done here, or on any
   machine that has it):
   ```bash
   composer install --no-dev --optimize-autoloader
   npm install && npm run build
   ```
   This fills in `vendor/` and `public/build/`, which normally stay out of
   git but must ship to a host with no Composer/Node.
3. **Upload everything** in `businessflow/` (via cPanel File Manager or FTP)
   to a folder on the server, e.g. `businessflow/`. Point the domain or
   subdomain's document root at `businessflow/public`.
4. **In cPanel, create a MySQL database + user** and note the host, database
   name, username, password (usually `localhost` / `3306`).
5. **Visit `https://yourdomain.com/install?token=<your INSTALL_TOKEN>`.**
   Fill in the app URL and the MySQL details from step 4, submit. This
   writes `.env`, runs the migrations, and links storage — no terminal
   needed. It locks itself (`storage/app/installed.lock`) after success, so
   it can't be run a second time.
6. **Reminders/notifications without Redis:** add a cron job in cPanel
   (Cron Jobs) running every minute:
   ```
   * * * * * cd /home/USER/businessflow && php artisan schedule:run >> /dev/null 2>&1
   ```

If the host *does* offer "Setup PHP App" / Application Manager with a
Composer button, or actual SSH, that's simpler — skip straight to
`composer install`, `php artisan migrate --force`, `php artisan key:generate`
and use the same cron line above.

## Next up

Core CRM + sales documents (customers, leads, products/services, quotations,
invoices, payments) — same `BelongsToTenant` pattern, following the build
plan's module order.
