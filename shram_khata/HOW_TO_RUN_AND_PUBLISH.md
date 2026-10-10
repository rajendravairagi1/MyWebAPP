# HazriBook: chalane aur publish karne ke steps (Windows)

## A. Computer me taiyari (ek baar)
1. Zip par right-click > **Extract All**. Ek folder `shram_khata` banega (andar `pubspec.yaml` dikhni chahiye).
2. **Flutter**: https://docs.flutter.dev/get-started/install/windows/mobile se Flutter SDK zip download karo.
   `C:\src\flutter` me extract karo (Program Files me nahi).
   Start menu > "Edit the system environment variables" > Environment Variables > Path > New > `C:\src\flutter\bin`.
3. **Android Studio** https://developer.android.com/studio se install karo. Pehli baar kholne par "Standard" setup chuno, wo Android SDK khud install kar dega.
   Phir More Actions > SDK Manager > SDK Tools > **Android SDK Command-line Tools** tick karke Apply.
4. Naya Command Prompt kholo:
   - `flutter doctor --android-licenses` (sab par `y` dabao)
   - `flutter doctor` (Android toolchain par tick aana chahiye)

## B. Project kholna
- Android Studio > **Open** > `shram_khata` folder chuno (wahi jisme pubspec.yaml hai).
- Neeche Terminal tab me: `flutter pub get`

## C. Apne mobile me chalana
1. Phone: Settings > About phone > **Build number** par 7 baar tap > Developer options > **USB debugging** ON.
2. USB se computer se jodo, phone par "Allow" dabao.
3. `flutter devices` (phone ka naam dikhna chahiye), phir `flutter run`. App phone me install ho jayegi.
4. Dusron ko dene ke liye APK: `flutter build apk --release`.
   File: `build\app\outputs\flutter-apk\app-release.apk`. WhatsApp se bhejo; phone me "Install unknown apps" allow karna padega.

## D. Play Store par publish
1. **Developer account**: https://play.google.com/console, ek baar ka fee $25, identity verification.
   Naye personal account ko production se pehle closed testing me kuch testers (filhal 12) 14 din tak chahiye. Current rule Play Console me check karo.
2. **Upload key banao** (ek baar). Command Prompt me:
   `"C:\Program Files\Android\Android Studio\jbr\bin\keytool" -genkey -v -keystore C:\Users\AAPKA_NAAM\upload-keystore.jks -storetype JKS -keyalg RSA -keysize 2048 -validity 10000 -alias upload`
   Password yaad rakho. **Is .jks file aur password ka backup rakho**, ise kisi ko mat do.
3. `android\key.properties.example` ko copy karke `android\key.properties` banao aur apne password/path bharo (path me `/` use karo, jaise `C:/Users/NAAM/upload-keystore.jks`).
4. `pubspec.yaml` me `version: 1.0.0+1`. Har naye upload par `+1` badhao (`1.0.0+2`).
5. Build: `flutter build appbundle --release`
   File: `build\app\outputs\bundle\release\app-release.aab`
6. Play Console > **Create app**. Bharna hoga:
   - Store listing: naam, chhota aur poora description, **app icon 512x512**, **feature graphic 1024x500**, kam se kam 2 phone screenshots.
   - **Privacy policy URL** (zaroori).
   - Data safety form, content rating, target audience, app access (reviewer ke liye login ka tareeka).
7. **Testing > Closed testing** me AAB upload karo, testers ke email jodo. Shuruaat me yahi karo.
8. Sab theek hone par **Production > Create release** me wahi AAB upload karke review ke liye bhejo. Review me kuch din lag sakte hain.

## Dhyan do
- `applicationId` (`com.oneweblink.hazribook`) Play Store par ek baar chhapne ke baad badal nahi sakta.
- Abhi data sirf phone me rehta hai. Phone kho gaya to data jayega. More > Backup & export se copy rakho.
