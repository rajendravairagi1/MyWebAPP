PRO BUILDER CRM — MARKETING WEBSITE (Laravel / PHP version)
====================================================

WHAT THIS IS
-------------------
This is the ProBuilderCRM.com marketing website, built in Laravel/PHP — the
same technology as your BusinessFlow CRM app, so it deploys the exact same
way: upload a zip via cPanel File Manager, no Terminal/SSH needed.

It replaces the earlier Next.js (Node.js) version, because your hosting
plan does not support Node.js. Nothing here needs Node — it's plain PHP.

It has a real database-backed blog you can post to from /admin, dynamic
pricing you can edit yourself, and proper SEO (sitemap, robots.txt,
structured data, llms.txt for AI answer engines).

HOW TO DEPLOY (cPanel, no Terminal needed)
-------------------
1. In cPanel, make sure ProBuilderCRM.com (or a subdomain like
   www.probuildercrm.com) is set up, pointing at its own folder — e.g.
   probuildercrm_site (NOT public_html directly).

   IMPORTANT — Laravel apps need their "Document Root" to point at the
   app's public/ folder, not the app's root folder. When creating the
   domain/subdomain in cPanel (Domains → Domains → Create A New Domain),
   there is a "Document Root" field — set it to:
       probuildercrm_site/public
   (adjust the folder name to whatever you named it in step 1 above.)

2. Upload this zip into probuildercrm_site/ (one level above public/) and
   extract it there. You should end up with folders like app/, public/,
   vendor/, routes/ directly inside probuildercrm_site/ — if extracting
   created an extra subfolder, move everything up one level.

3. In cPanel File Manager, right-click these two folders and set
   Permissions to 755 (or 775 if 755 doesn't work) — Laravel needs to
   write to them:
     - storage/ (and everything inside it)
     - bootstrap/cache/

4. Open the .env file (in File Manager, or Edit) and change these two
   lines to your own values:
     ADMIN_PASSWORD=<a real password only you know>
     APP_URL=https://probuildercrm.com   (or your actual domain)

   Leave everything else in .env as it is — APP_KEY and INSTALL_TOKEN are
   already generated for you.

5. Visit this URL once in your browser (replace with your real domain and
   keep the token exactly as it is in your .env file's INSTALL_TOKEN
   line):
     https://probuildercrm.com/migrate?token=PASTE_YOUR_INSTALL_TOKEN_HERE

   This sets up the database (blog + pricing tables) and the default
   pricing plans. You'll see a small text page confirming it ran — that's
   normal, it's not a "real" page.

6. Visit https://probuildercrm.com — it should load.

AFTER EVERY FUTURE UPDATE
-------------------
Whenever I send you a new zip for this site:
1. Upload and extract it the same way (step 2 above) — this overwrites
   the code but never touches your database (blog posts, pricing, admin
   password all stay exactly as they are).
2. Visit the same /migrate?token=... URL again (step 5) — this applies
   any new database changes. If a deploy has none, it's harmless to run
   anyway.

You do NOT need to redo steps 1, 3, or 4 on every update — only step 2's
folder upload and step 5's /migrate visit.

BEFORE YOU GO LIVE
-------------------
1. LOGO — once you send me the real logo file, I'll wire it into the
   header/footer properly.

2. WHATSAPP NUMBER / EMAIL — set in config/site.php if you ever want to
   change them yourself; otherwise just tell me the new number/email.

3. PRICING — Solo ₹999, Builder Team ₹2,499, Company ₹4,999 per month are
   set as a starting point. Change any of these yourself any time from
   /admin → Pricing tab — no need to ask me. The 6-month price (+1 month
   free), yearly price (+2 months free), and the permanent "40% OFF"
   badge are all worked out automatically from whatever monthly price
   you set.

4. ADMIN PASSWORD — make sure you actually changed ADMIN_PASSWORD in
   step 4 above to something real before going live.

USING THE ADMIN PANEL (day to day)
-------------------
- Go to https://probuildercrm.com/admin
- Log in with the ADMIN_PASSWORD you set
- "Blog Posts" tab — "+ New Post" to add an article (Title, Category,
  Author, Date, Read time, a short Excerpt, and the content itself as
  Paragraph/Heading/List blocks you can add, remove and reorder). Edit
  or Delete any post the same way, any time — changes appear on /blog
  immediately, no redeploy needed.
- "Pricing" tab — click "Edit" on any plan to change its monthly price,
  with a live preview showing exactly what visitors will see for all
  three billing cycles before you save.

AFTER IT'S LIVE
-------------------
- Go to Google Search Console (search.google.com/search-console), add
  the probuildercrm.com property, and submit
  https://probuildercrm.com/sitemap.xml
- Every blog post you add is automatically included in the sitemap.
