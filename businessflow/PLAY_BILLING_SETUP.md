# Google Play Billing — Manual Setup Steps

Ye sab steps **sirf aapko** karne hai (Play Console + Google Cloud
account ke through) — code side ka kaam ho chuka hai. Har step ke baad
jo bhi value milti hai (fingerprint, JSON file, IDs) wo mujhe bhej dena,
main `.env` me set kar dunga.

Order important hai — upar se niche follow karo.

---

## 1. PWABuilder se Android package banao

1. https://www.pwabuilder.com kholo
2. URL me daalo: `https://app.probuildercrm.com`
3. "Start" dabao — ye already-existing manifest/service worker ko scan
   karega (ye already ready hai, koi error nahi ana chahiye)
4. "Android" package select karo
5. Options me **"Enable Google Play Billing"** ka toggle ON karo — ye
   automatically Digital Goods API wiring add kar dega, koi extra code
   nahi likhna padega
6. Package name set karo: `com.probuildercrm.app` (ya jo bhi naam
   pasand ho, bas mujhe bata dena taki config me wahi set karu)
7. Download kar lo — isme ek **signed .aab file** milegi (Play Store
   upload ke liye) aur ek `assetlinks.json` reference

## 2. Play Console account + app listing

1. https://play.google.com/console pe jao, Play Console developer
   account banao ($25 one-time fee, Google charge karega)
2. "Create app" — naam "Pro Builder CRM" (ya jo bhi pasand ho)
3. App listing ke basic details bharo (description, screenshots — wahi
   iPhone-style mockup images maine banayi thi, unse Android frame bhi
   bana sakte hai agar chahiye)
4. Step 1 ki `.aab` file yaha upload karo (Production ya pehle Internal
   Testing track me — testing track se shuru karna safer hai)

## 3. Fingerprint set karna

1. Upload ke baad Play Console **"Play App Signing"** section me jao
2. Wahan ek **SHA-256 certificate fingerprint** dikhega — puri string
   copy kar lo
3. Ye mujhe bhej do — main `.env` me `ANDROID_SHA256_FINGERPRINTS` set
   kar dunga (aur `ANDROID_PACKAGE_NAME` bhi, jo Step 1 me tय kiya tha)

## 4. Subscription products banana

Play Console me **Monetize → Products → Subscriptions** me jao aur
teen subscription products banao — **Product ID bilkul yehi rakhna**
(code isi naam se match karta hai):

| Product ID              | Hamara plan       |
|--------------------------|-------------------|
| `probuildercrm_solo`     | Solo              |
| `probuildercrm_team`     | Builder + Team    |
| `probuildercrm_company`  | Company           |

Har ek ke liye price + billing period (monthly/yearly) set kar do jo
bhi aap charge karna chahte ho.

(Agar in IDs ke alawa kuch aur naam use karna hai to koi baat nahi —
bas mujhe exact naam bata dena, main `config/services.php` me
`product_plan_map` update kar dunga.)

## 5. Google Cloud service account (server verification ke liye)

1. https://console.cloud.google.com pe jao, naya project banao (ya
   Play Console ke saath already-linked project use karo)
2. **APIs & Services → Library** me jao, "Android Publisher API" search
   karke **Enable** karo
3. **APIs & Services → Credentials → Create Credentials → Service
   Account** — koi bhi naam de do (e.g. "play-billing-server")
4. Service account create hone ke baad, uske "Keys" tab me jao →
   **Add Key → Create new key → JSON** — ye ek `.json` file download
   karega
5. **Ye JSON file mujhe bhej do** (ye sensitive hai — sirf server pe
   jayegi, kahi aur share mat karna)

## 6. Service account ko Play Console me link karna

1. Play Console me **Users and permissions** me jao
2. "Invite new user" — Step 5 wala service account ka email address
   daalo (JSON file ke andar `client_email` field me milega)
3. Permissions me **"View app information"** aur **"Manage orders and
   subscriptions"** dono ON karo, aur jo app banayi hai usko access do

## 7. Real-time notifications (Pub/Sub) — renewals automatically track karne ke liye

1. Google Cloud Console me **Pub/Sub → Topics → Create Topic** — naam
   kuch bhi (e.g. `play-billing-notifications`)
2. Us topic pe **Create Subscription**:
   - Delivery type: **Push**
   - Endpoint URL: `https://app.probuildercrm.com/api/webhooks/google-play-rtdn`
   - "Enable authentication" ON karo, Step 5 wala service account select
     karo — ye ek "Audience" field dikhayega, wo value mujhe bhej dena
     (`GOOGLE_PLAY_RTDN_AUDIENCE` env var me set hogi)
3. Play Console me **Monetize setup → Real-time developer notifications**
   me jao, us topic ka naam paste karo (format:
   `projects/<project-id>/topics/play-billing-notifications`)

---

## Jo bhi mujhe bhejna hai, ek jagah:

- [ ] Package name (Step 1)
- [ ] SHA-256 fingerprint (Step 3)
- [ ] Service account `.json` file (Step 5)
- [ ] Pub/Sub subscription ka "Audience" value (Step 7)

In char cheezon ke aane ke baad, main `.env` file update kar dunga aur
sab kuch live ho jayega — koi aur code change nahi lagega.
