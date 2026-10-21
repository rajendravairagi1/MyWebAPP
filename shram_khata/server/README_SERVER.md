# HazriBook server (Hissa 1: login + sync)

Do hisse hain:

- `database/schema.sql` — sirf phpMyAdmin mein import karna hai. Website folder mein upload NAHI karna.
- `upload/` — ye sab files website ke folder (subdomain ke Document Root) mein jaati hain.

## Steps (cPanel)

1. **Naya password:** cPanel → MySQL Databases mein database user ka password badla hua hona chahiye.
2. **Tables banao:** cPanel → phpMyAdmin → left mein `onewebli_hazribook` database chuno → upar **Import** →
   `database/schema.sql` chuno → **Import / Go**. 5 tables ban jaayenge: agencies, users, tokens, attempts, records.
3. **Files upload:** File Manager → `hazribook.oneweblink.com` ka Document Root → **Upload** → `upload` folder ke andar ki
   sab cheezein (index.php, .htaccess, config.sample.php, lib folder) us folder mein daalo.
   `.htaccess` hidden file hai: File Manager ki Settings mein **Show Hidden Files (dotfiles)** on karo.
4. **config.php banao:** `config.sample.php` ki copy banao, naam `config.php` rakho, Edit karke
   `db_pass` mein apna **naya** database password likho. Baaki waise hi rehne do. Save karo.
5. **Test:** browser mein `https://hazribook.oneweblink.com/v1/health` kholo.
   Aisa dikhna chahiye: `{"ok":true,"service":"hazribook",...,"db":true}`
   - `"db":false` aaye to config.php ka password/db naam galat hai, ya user ko database par ALL PRIVILEGES nahi mile.

## Update 2 (agar pehle wali files upload kar chuke ho)

1. phpMyAdmin → database chuno → **Import** → `database/update_2_feedback.sql` (sirf 2 naye tables banate hain).
2. `upload` folder ki files dobara upload karo (purani files replace hongi): `index.php`, `lib/sync.php`, `lib/util.php`,
   `lib/feedback.php` (nayi file). **`config.php` ko mat chhedo.**
3. Test: `https://hazribook.oneweblink.com/v1/health` → `"db":true`.

## API (app ke liye)

| Endpoint | Kaam |
|---|---|
| `GET /v1/health` | server aur database chal rahe hain? |
| `POST /v1/auth/register` | nayi agency + owner (businessName, ownerName, email/mobile, password, planId) |
| `POST /v1/auth/login` | identifier (email ya mobile) + password → token |
| `POST /v1/auth/logout`, `/v1/auth/password` | |
| `GET /v1/me` | |
| `POST /v1/sync/push` | phone ke badlaav server par |
| `GET /v1/sync/pull?since=N` | server ke naye badlaav phone par |
| `GET/POST /v1/users`, `POST /v1/users/{id}` | owner staff / manager banata hai, band karta hai |
| `POST /v1/feedback`, `GET /v1/feedback`, `POST /v1/feedback/read` | company manager ka message, owner/supervisor ko dikhta hai |
| `POST /v1/account/delete` | owner poora cloud data mita sakta hai (password + DELETE) |

Safety: password bcrypt se hash hota hai, token sirf hash ke roop mein rakha jaata hai, login par 8 galat koshish ke baad
15 minute ka rok, sirf https, har agency ka data alag, manager account ko sync nahi milta.
