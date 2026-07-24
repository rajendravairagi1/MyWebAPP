# Shivani Enterprises — Sales Ledger &amp; CRM

Pure PHP (no framework, no Composer) + MySQL sales ledger / CRM system, built to
run on ordinary shared hosting (cPanel). Two roles: **Super Admin** and
**Admin**.

## Features

**Super Admin**
- Create/disable admin accounts, reset their passwords
- Manage products and default prices
- View/allot customers to any admin, add customers directly
- View every sale, every payment, across all admins, with filters
  (admin, product, customer/mobile/invoice search, date range)
- Global ledger: every customer's given/paid/balance, filter by admin or
  "only balance due"
- Admin-wise report: pick an admin, see their totals, product-wise sales,
  and their customer list with balances
- Follow-ups/commitments roll-up across all admins
- Company settings (name, phone, address, invoice prefix)
- One-click manual DB backup + daily automatic backup via cron, download
  past backups

**Admin**
- Manage only their own customers (super admin allots customers to them,
  or they add new ones directly)
- Duplicate-mobile check when adding a customer (mobile number is unique
  across the whole system)
- Customer profile: photo, shop name, place, address, alternate mobile,
  document uploads (ID proof, agreements, etc.), full sale/payment
  history, running balance
- Create sales/invoices with multiple product lines; price per line is
  manually editable (for on-the-spot bargaining/discounts)
- Record payments (full or partial) against a customer, optionally
  tagged to a specific invoice
- Auto-generated invoice numbers, PDF invoice download, PDF customer
  statement (full ledger) download
- One-click WhatsApp reminder message (opens WhatsApp with the message
  pre-filled; attach the downloaded PDF manually — WhatsApp's click-to-chat
  links can't auto-attach files)
- Follow-ups/commitments with date+time, marked pending/done/cancelled,
  shown on the dashboard as "due today/overdue"
- Reports: date-range sales, product-wise breakdown, top customers by
  balance due

**Security**
- Passwords hashed with bcrypt (`password_hash`/`password_verify`)
- All DB queries use PDO prepared statements (no SQL injection)
- CSRF token on every form
- Session cookies are HttpOnly + SameSite=Lax (+ Secure on HTTPS), with
  idle timeout and periodic session ID rotation
- Role checks on every page (admins can never see another admin's data)
- Uploaded files are validated by real MIME type, renamed randomly, and
  served from a folder where PHP execution is blocked via `.htaccess`
- `config/`, `database/`, `includes/`, `lib/`, `backups/` are all blocked
  from direct web access via `.htaccess`

## Requirements

- PHP 8.0+ with `pdo_mysql` and `fileinfo` extensions (standard on cPanel)
- MySQL / MariaDB database
- No Composer, no Node — plain PHP files, upload and go

## Setup (cPanel shared hosting)

1. **Create a MySQL database** in cPanel (MySQL Databases), and a DB user
   with all privileges on it. Note the host (usually `localhost`), DB
   name, username, password.
2. **Import the schema**: cPanel → phpMyAdmin → select your database →
   Import → choose `database/schema.sql` → Go. This creates all tables
   and one default super admin login:
   - Username: `superadmin`
   - Password: `Admin@123`
   - **Change this password immediately after first login** (use the
     "Change Password" link in the sidebar).
3. **Upload the files**: this build is pre-configured for
   `shivani.oneweblink.com`. In cPanel, create that subdomain (Domains →
   Create A New Domain → `shivani.oneweblink.com`), then upload the
   *contents* of the `shivani-enterprises` folder into that subdomain's
   document root (e.g. `~/shivani.oneweblink.com/`) — not into a further
   subfolder, so `login.php` sits directly in that folder.
4. **Configure the app**: copy `config/config.sample.php` to
   `config/config.php`. `APP_URL` and `APP_SECRET` are already filled in
   for `shivani.oneweblink.com` — you only need to fill in the real DB
   host/name/user/password from step 1.
5. Visit `https://shivani.oneweblink.com/login.php` and log in with the
   super admin account.
6. In **Settings**, set your real company name/phone/address and invoice
   prefix.
7. In **Admins**, create your admin user accounts.
8. In **Products**, add your products (coolers etc.) with default prices.

## Daily backups

Go to **Backup** in the super admin menu for one-click manual backups,
and instructions for setting up a daily cron job in cPanel (Advanced →
Cron Jobs) pointing at `cron/daily_backup.php`. Backups are plain `.sql`
dump files stored in `/backups` (not web-accessible) and the last 14 are
kept automatically. Download important backups off-server periodically
(e.g. to your computer or Google Drive) for extra safety.

## Notes on WhatsApp

This uses WhatsApp's free "click-to-chat" links (`wa.me`), which open a
chat with a pre-filled text message — there's no paid WhatsApp Business
API involved. It cannot auto-attach the PDF file (WhatsApp doesn't allow
that from a simple link); download the invoice/statement PDF first, then
use the WhatsApp button and attach the file manually in the chat that
opens.

## Folder structure

```
config/          DB + app config (config.php is gitignored, never share it)
database/        schema.sql to import
includes/        core PHP (db, auth, functions, layout)
lib/             self-contained PDF generator + invoice/statement rendering
superadmin/      super admin only pages
admin/           admin only pages
customer_form.php, customer_view.php, sale_form.php, sale_view.php,
payment_form.php, followup_form.php   shared pages used by both roles
                 (permission-checked per request)
uploads/         customer photos + documents (web-inaccessible as PHP)
backups/         database backups (web-inaccessible)
cron/            daily_backup.php for cPanel cron jobs
```
