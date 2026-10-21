# Play Console: Data safety form (cloud sync wala version)

Play Console → App content → **Data safety**. Cloud sync ke baad "No data collected" nahi chalega. Ye jawab dena:

**Does your app collect or share user data?** → Yes (collect). Share: **No** (data kisi third party ko nahi jaata).

**Is all of the user data collected by your app encrypted in transit?** → Yes (HTTPS).
**Do you provide a way for users to request that their data be deleted?** → Yes.
  - In-app: More → Cloud sync → "Delete my cloud account and data".
  - Web link: privacy policy page (contact email).

Data types (sab "Collected", "Not shared", purpose = **App functionality**, "Optional" = Yes kyunki cloud sync optional hai):
- Personal info → Name, Email address, Phone number (owner/team/manager login + workers ke naam/number).
- Financial info → Other financial info (payments, advances, invoices).
- Photos and videos: **Not collected** (photos phone par hi rehti hain).
- App activity / Other user-generated content → Attendance, messages (company manager ka feedback).

Account creation: **Yes, username + password**. Account deletion URL: privacy policy ka link
(`https://hazribook.oneweblink.com/privacy` jaisa koi page bana ke daalna, ya policy wali HTML ka link) + email.

Privacy policy URL: `store_assets/privacy-policy.html` ko website par daalkar uska link do. Isme `YOUR-EMAIL@example.com`
aur `YOUR BUSINESS NAME` apni asli details se badal do.

Play ko test karne ke liye login chahiye to "App access" mein ek demo account (owner email + password) likho.
