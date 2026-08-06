# Pehli Baar Install Karna — Poora Step-by-Step

Ye guide bilkul shuru se hai — pehli baar cPanel pe kuch install kar rahe ho to bhi follow ho jayega.

## Pehle Ye Confirm Karo (Hosting Me)
1. cPanel login kholo apne hosting provider ki site se (jaise Hostinger, Bluehost, GoDaddy)
2. cPanel ke andar **"MultiPHP Manager"** dhundo (search box me "PHP" type karo agar nahi mile)
3. Usme check karo PHP version **8.2 ya usse upar** set hai — agar nahi hai to yahi se badal do
4. MySQL Database ka option hona chahiye — "MySQL Databases" naam se milega

---

## Step 1 — ZIP Upload Karo

1. cPanel me **"File Manager"** kholo
2. Upar left me apna account root dikh raha hoga — jaise `home` ya `public_html` ke bahar wala level
3. **`public_html` ke ANDAR mat jana** — ek level bahar hi raho (jahan `public_html` folder khud dikh raha ho)
4. Wahan **"+ Folder"** button se naya folder banao, naam do: `erp-system`
5. `erp-system` folder ke andar jao
6. Upar **"Upload"** button click karo, jo ZIP file maine bheji hai wo select karo, upload hone do (thoda time lagega, file badi hai)
7. Upload hone ke baad File Manager me wapas jao, ZIP file pe **right-click → Extract** karo
8. Extract hone ke baad ZIP file delete kar do (space bachane ke liye)

**Check karo:** `erp-system` folder ke andar `app`, `public`, `routes`, `.env` jaisi cheezein dikhni chahiye — direct nahi to shayad ek extra sub-folder ban gaya hoga, uske andar se sab files bahar `erp-system` me move kar do.

---

## Step 2 — Database Banao

1. cPanel me **"MySQL Databases"** kholo
2. **"Create New Database"** me naam do — jaise `erp` (cPanel isme aapka username prefix laga dega, jaise `yourusername_erp` — ye pura naam yaad rakhna)
3. Neeche **"MySQL Users"** section me naya user banao — username + strong password (password kahi likh lo, baad me chahiye)
4. **"Add User to Database"** me jo user banaya wahi database se link karo, aur **"All Privileges"** check karke Save karo

**3 cheezein yaad rakho:** Database ka pura naam, Username ka pura naam, Password.

---

## Step 3 — Domain/Subdomain Ko `public` Folder Se Jodo

Ye important step hai — website ko sirf `erp-system` ke andar wale **`public`** folder tak access dena hai, baaki sab files hidden rehni chahiye (security ke liye).

**Agar subdomain use karna hai** (jaise `erp.yourcompany.com`):
1. cPanel me **"Subdomains"** kholo
2. Naya subdomain banao — naam `erp`, domain apna select karo
3. Jo **"Document Root"** field aayegi usme likho: `erp-system/public`
4. Create/Save karo

**Agar seedha apne main domain pe chahiye** (jaise `yourcompany.com`):
1. cPanel me **"Domains"** kholo
2. Apne domain ke saamne **"Document Root"** ka pencil/edit icon dabao
3. Value ko `erp-system/public` kar do, Save karo

---

## Step 4 — `.env` File Me Apni Database Details Bharo

1. File Manager me `erp-system` folder me jao
2. Upar **"Settings"** (right side) → **"Show Hidden Files"** ON karo (`.env` file dot se shuru hoti hai, isliye chhupi rehti hai)
3. `.env` file dhundo, usko **right-click → Edit** se kholo
4. Ye lines dhundo aur badlo:

```
APP_ENV=production
APP_DEBUG=false
APP_URL=https://erp.yourcompany.com
```
(`APP_URL` me apna asli subdomain/domain daalo)

5. Thoda neeche ye block dhundo:

```
DB_CONNECTION=sqlite
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=laravel
# DB_USERNAME=root
# DB_PASSWORD=
```

Isko **poora replace** kar do (Step 2 me jo database details banayi thi wo yaha daalo):

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=yourusername_erp
DB_USERNAME=yourusername_erpuser
DB_PASSWORD=aapka_wo_password
```

6. **Save** kar do (File Manager editor me upar "Save Changes" button hoga)

---

## Step 5 — Setup Chalao (Browser Se, Terminal Ki Zaroorat Nahi)

Is app ke andar ek **special one-time setup page** bana ke rakha hai — isse database table ban jayenge aur pehla Owner login create ho jayega, sab kuch browser se, bina Terminal/command line ke.

1. Apna browser kholo
2. Ye URL open karo (apna domain/subdomain daal ke):

```
https://erp.yourcompany.com/setup?token=first-install-2026
```

3. Ek chhota page khulega — usme **"Run Setup"** button dikhega, use click karo
4. Neeche green text me log dikhega — sab lines **"OK"** se start honi chahiye, aakhir me likha hoga **"SAB DONE"**

**Agar "FAIL — Database connect nahi hua" dikhe** — matlab Step 4 me `.env` ki database details galat hain, wapas check karo (database name, username, password — teeno bilkul waise hi hone chahiye jaise cPanel me bane the).

---

## Step 6 — Login Test Karo

Ab ye URL kholo:

```
https://erp.yourcompany.com/login
```

- **Email:** `owner@example.com`
- **Password:** `password`

Login ho jaye to left/top menu me apna naam dikhega, aur agar `/roles` URL khologe to sab roles ki list dikhegi.

**⚠️ Turant password badal do** — Login karke `/profile` page pe jao, wahan se naya password set kar do. `owner@example.com / password` sirf pehli baar ke liye tha, isse kisi ko login nahi karne dena.

---

## Step 7 — Setup Page Delete Kar Do (Zaroori — Security)

Setup complete hone ke baad, File Manager me jaake ye 2 cheezein delete kar do (dobara delete nahi hongi to koi bhi wo `/setup` URL kholke database dobara chhed sakta hai):

1. `erp-system/routes/setup.php`
2. `erp-system/app/Http/Controllers/SetupController.php`

Delete karne ke baad browser me `/setup` URL khol ke confirm karo — ab "404 Not Found" ya "500" error aana chahiye, matlab safely band ho gaya.

---

## Sab Ho Gaya — Ab Kya

Aap login karke `/roles` page pe jao, alag-alag roles pe click karke dekho checkbox system kaise kaam karta hai. Isi pe hum aage baaki modules (Customer, Job Work, Stock, Invoice) banayenge.

## Kahin Bhi Atke To

Screenshot bhej do jo bhi error/page dikhe — main dekh ke exact batunga kya karna hai.
