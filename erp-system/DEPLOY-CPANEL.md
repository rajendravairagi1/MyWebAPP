# cPanel Pe Deploy Kaise Karein (Step-by-Step)

## Requirement Check (Hosting Me)
- PHP **8.2 ya usse upar** (cPanel me "MultiPHP Manager" se select kar sakte ho)
- MySQL Database

---

## Step 1 — ZIP Upload Karo

1. cPanel → **File Manager** → apni account root me jao (`public_html` ke bahar, ek level upar — jaise `/home/yourusername/`)
2. Wahan ek naya folder banao: `erp-system`
3. ZIP file usme upload karo, **Extract** kar do
4. `public_html` ke andar mat rakhna — security ke liye poora app code bahar rehna chahiye, sirf `public` folder web se accessible hona chahiye (Step 3 me isko wire karenge)

---

## Step 2 — Database Banao

1. cPanel → **MySQL Databases**
2. Naya database banao (jaise `yourname_erp`)
3. Naya database user banao, password set karo
4. User ko database se **All Privileges** ke saath attach karo

---

## Step 3 — Domain/Subdomain Point Karo `public` Folder Pe

Ye sabse important step hai — is app ka **sirf `public` folder** browser se accessible hona chahiye, baaki sab andar rehna chahiye.

**Agar aap subdomain use kar rahe ho** (jaise `erp.yourcompany.com`):
1. cPanel → **Subdomains** → naya subdomain banao
2. **Document Root** field me daalo: `erp-system/public`
3. Save karo — bas ho gaya

**Agar main domain pe hi chahiye** (jaise `yourcompany.com`):
1. cPanel → **Domains** → apne domain ka **Document Root** edit karo
2. `public_html` ki jagah `erp-system/public` daal do

---

## Step 4 — `.env` File Set Karo

File Manager me `erp-system/.env` file kholo (agar dikh nahi rahi to "Settings" me "Show Hidden Files" ON karo), aur ye values badlo:

```
APP_ENV=production
APP_DEBUG=false
APP_URL=https://erp.yourcompany.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=yourname_erp
DB_USERNAME=yourname_erpuser
DB_PASSWORD=aapka_password_yaha
```

(Sqlite line `DB_CONNECTION=sqlite` ko `mysql` se replace karo, aur upar DB_HOST/PORT/DATABASE/USERNAME/PASSWORD lines ka `#` hata do agar comment hai.)

---

## Step 5 — Terminal Se Setup Commands (Agar cPanel Terminal Milta Hai)

Agar aapki hosting me cPanel **Terminal** option hai (Hostinger Business+, ya kai VPS-based shared hosting me hota hai):

```bash
cd ~/erp-system
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
```

## Agar Terminal Nahi Hai (Basic Shared Hosting)

Kai hosting **cPanel → Cron Jobs** ya **"Setup Node.js/PHP App"** se ek baar command chala sakti hain, ya support team se PHP CLI access maango. Agar bilkul nahi milta, mujhe batao — main ek **web-based one-time setup script** bana dunga jo browser se URL kholte hi ye sab kar dega (setup ke baad delete kar dena).

---

## Step 6 — Test Login

Apna domain/subdomain kholo, `/login` pe jao:

- **Email:** `owner@example.com`
- **Password:** `password`

**⚠️ Login hone ke baad turant password change kar lena** (`/profile` page se) — ye sirf test ke liye tha.

---

## Kuch Bhi Atke To

Isi conversation me bata dena — screenshot bhej dena error ka, main fix kar dunga.
