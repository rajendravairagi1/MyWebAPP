# Lead Management CRM (PHP + MySQL)

Simple, self-contained Lead CRM jo directly cPanel/shared hosting pe chal jaata hai.
Koi Composer/build step nahi chahiye — sirf PHP + MySQL.

## Setup Steps (cPanel)

1. **Database banao**
   - cPanel > MySQL Databases > naya database + user banao, user ko database se attach karo (ALL PRIVILEGES).
   - phpMyAdmin kholo, apna naya database select karo, **Import** tab se `database.sql` file upload karo.
   - Ye default admin user bana dega: username `admin`, password `ChangeMe123!` — login ke turant baad Settings page se ye password badal do.

2. **Files upload karo**
   - Is poori `php-crm` folder ko apne hosting ke `public_html` (ya kisi subfolder, e.g. `public_html/crm`) mein upload karo (FTP/File Manager se).

3. **`config/config.php` edit karo**
   - `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS` apne cPanel MySQL details se badlo.
   - `APP_SECRET` ko kisi bhi random long string se replace karo.
   - Agar live site HTTPS pe hai (usually hoti hai) to `APP_FORCE_HTTPS_COOKIE` ko `true` hi rehne do.

4. **Google Places API key**
   - Login karne ke baad, **Settings** page pe jaake apni Google Places API key daalo.
   - Key Google Cloud Console se milegi: "Places API" enable karo, phir Credentials se API key generate karo.
   - Bina key ke Search & Analyze page kaam nahi karega (baaki sab pages normal chalenge).

5. **Login**
   - `https://yourdomain.com/crm/login.php` (ya jahan bhi upload kiya) kholo.
   - `admin` / `ChangeMe123!` se login karo, turant Settings > Change Password se naya password set karo.

## Pages

- `dashboard.php` — totals, status-wise counts, follow-up list (contacted 7+ din se, reply nahi aaya).
- `search.php` — Google Places search, results, "Analyze & Save" (website check karke email/social/gaps nikalta hai, DB mein save karta hai — duplicate place_id automatically update ho jaata hai, naya row nahi banta).
- `leads.php` — All Leads: filter (status/search), sort, bulk status update, CSV export.
- `lead-detail.php` — pura lead detail, gaps, email template select karke "Mark as Contacted" (email_log mein entry banti hai), reply aane par "Mark as Replied" + notes, status dropdown, notes.
- `templates.php` — email templates CRUD (gap-type se link kar sakte ho).
- `settings.php` — API key, gap thresholds (reviews/rating cutoff), password change.

## Email Sending - Reality Check

Ye Phase 1 (manual) tarika use karta hai jaisa spec mein bola gaya:
- Tool khud email NAHI bhejta. `lead-detail.php` pe template se subject/body copy karke apne normal Gmail/Outlook se bhejo.
- Bhej diya? "Mark as Contacted" click karo — email_log mein record ban jayega, status "Contacted" ho jayega.
- Reply aaya apne inbox mein? "Mark as Replied" click karke notes likh do.

Baad mein agar chaaho to IMAP auto-check ya SendGrid/Brevo jaisi email API se automatic tracking add karwa sakte ho — abhi ke liye ye sabse reliable (100%) tarika hai.

## Security Notes

- Sab DB queries prepared statements (PDO) se hain — SQL injection se safe.
- Passwords `password_hash`/`password_verify` (bcrypt) se store/verify hote hain.
- Har form mein CSRF token hai.
- Sessions httpOnly + secure cookies use karte hain.
- `config/` folder aur `.sql`/`.md` files `.htaccess` se web se block hain (Apache hosting pe).
- Login fail hone par chhota delay hai (brute-force thoda slow karne ke liye) — production mein rate-limiting/captcha add karna aur behtar hoga agar public-facing ho.

## Requirements

- PHP 7.4+ (8.x recommended), PDO MySQL extension, cURL extension (Google Places calls ke liye) — ye sab standard cPanel hosting mein already enabled hote hain.
