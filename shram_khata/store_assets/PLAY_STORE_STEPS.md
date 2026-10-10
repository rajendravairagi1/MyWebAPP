# Play Store par Internal testing: step by step

Is folder me taiyar cheezein: `play_icon_512.png`, `feature_graphic_1024x500.png`, `screenshots/` (6 phone screenshots),
`privacy-policy.html`, `store-listing.md` (copy-paste text). App ka icon bhi project me lag chuka hai.

## Step 1: Developer account (aap karoge)
1. https://play.google.com/console par apne Google account se jao.
2. **Create developer account**: type chuno (Personal ya Organization), fee $25 (ek baar), naam, address, phone.
3. Identity verification (ID aur selfie) poori karo. Kuch din lag sakte hain.

## Step 2: Privacy policy ko website par daalo (URL chahiye)
1. `store_assets/privacy-policy.html` Notepad me kholo. `YOUR-EMAIL@example.com` aur `YOUR BUSINESS NAME, YOUR CITY` apni details se badlo.
2. Isse apni website par upload karo (cPanel > File Manager > public_html > Upload), jaise `https://aapki-site.com/shram-khata-privacy.html`.
3. Ye link browser me khol ke check karo. Yahi URL Play Console me dena hai.

## Step 3: Upload key banao (ek baar, bahut zaroori)
1. `flutter doctor -v` chalao aur "Java binary at:" wali line dekho. Usi folder me `keytool.exe` hota hai.
   Aam raasta: `C:\Program Files\Android\Android Studio\jbr\bin\keytool.exe`
2. PowerShell me (raasta apne hisaab se):
```
& "C:\Program Files\Android\Android Studio\jbr\bin\keytool.exe" -genkey -v -keystore C:\Users\Dell\upload-keystore.jks -storetype JKS -keyalg RSA -keysize 2048 -validity 10000 -alias upload
```
3. Password do (yaad rakho!), naam/shehar puchhe to bharo. Last me `yes` likho.
4. **`C:\Users\Dell\upload-keystore.jks` aur password ka backup rakho** (pen drive / email to self). Kisi ko mat do.
5. `shram_khata\android` folder me `key.properties.example` ko copy karke naam `key.properties` rakho aur kholke bharo:
```
storePassword=AAPKA_PASSWORD
keyPassword=AAPKA_PASSWORD
keyAlias=upload
storeFile=C:/Users/Dell/upload-keystore.jks
```
(path me `/` use karo, `\` nahi.)

## Step 4: App bundle (AAB) banao
`shram_khata` folder me PowerShell:
```
flutter build appbundle --release
```
File: `build\app\outputs\bundle\release\app-release.aab`

Zaroori: ye tabhi Play Store me chalegi jab Step 3 ka `key.properties` bana ho. Warna Play "debug key se signed" bol ke mana kar dega.

## Step 5: Play Console me app banao
1. **Create app**: App name `LabourBook: Attendance & Pay`, language English (India), **App**, **Free**, declarations tick karo.
2. Left menu me **Dashboard** ke "Set up your app" ki list ek-ek karke bharo. Saare jawab `store-listing.md` me likhe hain:
   - Privacy policy (Step 2 ka URL)
   - App access, Ads (No), Content rating, Target audience (18+), Data safety
   - **Main store listing**: naam, short/full description copy karo; **App icon** = `play_icon_512.png`;
     **Feature graphic** = `feature_graphic_1024x500.png`; **Phone screenshots** = `screenshots` folder ki saari 6 files.
   - Category: **Business**, contact email.

## Step 6: Internal testing release
1. **Testing > Internal testing > Create new release**.
2. "Play app signing" aaye to **Continue / Accept** karo.
3. **Upload** par `app-release.aab` dalo.
4. Release name ko jaisa hai rehne do. Release notes me `store-listing.md` wali line paste karo. **Next > Save**.
5. **Testers** tab: **Create email list**, naam do, apna Gmail (jo phone me hai) jodo. Aur logon ko bhi jod sakte ho (100 tak).
6. **Review release > Start rollout to Internal testing**.
7. Testers tab ke neeche **Copy link** milega (opt-in URL). Ye link phone me kholo (usi Gmail se), **Become a tester** dabao, phir **Download it on Google Play**.
8. Play Store se app install hogi. **Advanced Protection band karne ki zaroorat nahi padegi.**

## Step 7: Aage chal kar (sabko dene ke liye)
- Naye personal account ko Production se pehle **Closed testing me 12 testers, 14 din tak** chahiye (rule badal sakta hai, Play Console me "Production access" me dekho).
- Phir **Production > Create release** me wahi AAB (ya naya) daalo.
- Har naye upload par `pubspec.yaml` me `version: 1.0.0+1` ko `1.0.0+2`, `+3` karo, warna Play reject karega.

## Dhyan
- `upload-keystore.jks` kho gaya to Play support se reset karwana padega. Backup zaroor rakho.
- Abhi data sirf phone me rehta hai. Phone badalne par **More > Backup & export** se copy lena.
