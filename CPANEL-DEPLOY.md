# Bluehost / cPanel par Deploy Karne Ka Tarika

Ye guide un logo ke liye hai jinke paas cPanel hosting hai jisme **Application Manager** (pehle "Setup Node.js App" kehlata tha) available hai — Bluehost ke zyadatar shared plans me ye hota hai.

**Zaroori:** Pehle **subdomain** par test karo (jaise `app.oneweblink.com` ya `new.oneweblink.com`), apni live WordPress site (`oneweblink.com`) ko seedha touch mat karo. Jab sab kuch sahi chal raha ho, tab main domain ko switch karna.

---

## Step 1 — Subdomain banao

1. cPanel me **Domains → Domains** kholo
2. **Create A New Domain** par click karo
3. Domain field me `app.oneweblink.com` (ya jo naam chaho) daalo — "Share document root" ka option uncheck rehne do, cPanel khud ek naya folder bana dega (e.g. `app.oneweblink.com`)
4. Submit karo

---

## Step 2 — Zip file upload aur extract karo

1. cPanel me **Files → File Manager** kholo
2. Andar jaake apne home folder me us naye subdomain wale folder me jao (e.g. `app.oneweblink.com`)
3. Upar **Upload** button se ye zip file upload karo
4. Upload hone ke baad File Manager me wapas us folder me jao, zip file par **right-click → Extract** karo
5. Extract hone ke baad zip ki saari files/folders (jaise `src`, `package.json`, `server.js`) us folder ke root me hone chahiye — agar ek extra subfolder ban gaya ho (jaise `MyWebAPP/MyWebAPP/...`), to us andar wali files ko ek level bahar move kar do

---

## Step 3 — Node.js app banao (Application Manager)

1. **Software → Application Manager** kholo
2. **Create Application** (ya "+") par click karo
3. Ye fields bharo:
   - **Node.js version** — sabse naya jo available ho (18 ya usse upar koi bhi chalega)
   - **Application mode** — Production
   - **Application root** — wahi folder jo Step 1 me bana tha (e.g. `app.oneweblink.com`)
   - **Application URL** — dropdown se wahi subdomain select karo
   - **Application startup file** — `server.js`
4. **Create** par click karo

Create hone ke baad cPanel ek command dikhayega jaisa ye:
```
source /home3/onewebli/nodevenv/app.oneweblink.com/20/bin/activate && cd /home3/onewebli/app.oneweblink.com
```
Ise **copy** kar lo — agle step me chahiye hoga.

---

## Step 4 — Terminal se install aur build karo

1. **Advanced → Terminal** kholo
2. Step 3 wala command paste karke Enter dabao (ye "virtual environment" me le jaata hai jahan sahi Node version active hota hai)
3. Ab ye commands ek-ek karke chalao:
   ```
   npm install
   npm run build
   ```
   (`npm install` thoda time lega — normal hai)
4. Agar koi error na aaye, to build ho gaya

---

## Step 5 — Environment variables set karo (optional but recommended)

Contact form se email bhejne aur `/admin` panel unlock karne ke liye:

1. File Manager me us app ke folder me jao
2. **+ File** se naya file banao naam: `.env`
3. Usme ye likho (apni values ke saath):
   ```
   ADMIN_TOKEN=koi_lamba_random_password
   RESEND_API_KEY=
   RESEND_TO_EMAIL=Info@oneweblink.com
   ```
   (`RESEND_API_KEY` khali chhod sakte ho abhi — bina uske form submissions log hote rahenge, email nahi jayega. Baad me [resend.com](https://resend.com) se free key le sakte ho.)
4. Save karo

---

## Step 6 — App start/restart karo

1. **Software → Application Manager** me wapas jao
2. Apni app ke saamne **Restart** (ya "Start") button dabao
3. Browser me apna subdomain kholo (e.g. `https://app.oneweblink.com`) — site chalni chahiye

---

## Step 7 — Sab check ho jaye to main domain par switch karna

Jab subdomain par sab sahi chal raha ho (Home, Services, Blog, Contact form, `/admin` panel), tab:

- **Domains → Zone Editor** ya **Domains** section me jaake `oneweblink.com` ko is nayi app ke folder se point karwa sakte ho, YA
- Purani WordPress site ka backup lekar, nayi app ko root domain par move kar sakte ho

Ye step thoda risky hai (purani site replace hogi), isliye jab ready ho tab bata dena — main exact steps de dunga.

---

## Kuch cheezein jo normal hain, ghabrana nahi

- `npm install` ke baad kuch "deprecated" warnings aa sakti hain — ignore kar sakte ho
- Pehli baar site khulte hi database (`data/app.db`) khud ban jaayega, koi extra step nahi chahiye
- Agar site nahi khulti, sabse pehle Application Manager me app ke "Logs" dekho — wahan error clearly dikhega
