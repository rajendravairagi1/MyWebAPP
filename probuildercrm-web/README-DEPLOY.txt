PRO BUILDER CRM — MARKETING WEBSITE (v2, dynamic)
====================================================

WHAT THIS IS
-------------------
This is a Next.js (Node.js) website — NOT plain HTML like the previous
version. It has a real database-backed blog you can post to from
/admin, and proper SEO (sitemap, robots.txt, structured data). It's
meant to run on its own domain: ProBuilderCRM.com — separate from the
CRM app itself (businessflow).

Because it's a Node.js app, it deploys differently than the CRM's PHP
zip-upload. cPanel has a dedicated feature for this called "Setup
Node.js App" — no Terminal/SSH needed, it's all buttons in cPanel.

HOW TO DEPLOY (cPanel "Setup Node.js App")
-------------------
1. In cPanel, make sure ProBuilderCRM.com is added as a domain/addon
   domain, pointing at its own folder — e.g. probuildercrm_web
   (NOT public_html directly, and NOT the same folder as the old
   static site).

2. Upload this zip into that folder and extract it there. You should
   end up with files like package.json, server.js, src/ directly
   inside probuildercrm_web/ (not nested one level deeper — if
   extracting created an extra subfolder, move everything up one
   level).

3. In cPanel, find "Setup Node.js App" and click "Create Application":
   - Node.js version: 18 or newer (20 is fine if offered)
   - Application mode: Production
   - Application root: the folder from step 2 (e.g. probuildercrm_web)
   - Application URL: probuildercrm.com
   - Application startup file: server.js
   - Click "Create"

4. Still on that app's page, scroll to "Environment Variables" and add:
     ADMIN_PASSWORD = <a real password you choose>
   This is the password for /admin (where you post blog articles) —
   pick something only you know, it's not shown anywhere on the site.

5. Click the "Run NPM Install" button on that same page (installs the
   site's dependencies — this replaces `npm install` since there's no
   Terminal). Wait for it to finish. The site itself is already built
   (the .next folder in this zip) — you do NOT need to run `npm run
   build` anywhere, NPM Install + Restart is all it takes.

6. Click "Restart" on the app. Visit https://probuildercrm.com — it
   should load.

BEFORE YOU GO LIVE
-------------------
1. LOGO — right now the header/footer show a simple text logo
   ("P  Pro Builder CRM"). Once you have a real logo file, send it to
   me and I'll wire it in properly (or drop it in the public/ folder
   as logo.png/logo.svg and tell me the filename).

2. WHATSAPP NUMBER / EMAIL — set in src/data/site.js if you ever need
   to change them yourself; otherwise just tell me the new number/email
   and I'll update it.

3. PRICING — the numbers (Solo ₹999, Team ₹2,499, Company ₹4,999 per
   month) are the same draft numbers from the old static site. Tell me
   your real prices whenever you're ready and I'll update them.

4. ADMIN PASSWORD — make sure you actually set ADMIN_PASSWORD in step 4
   above to something real before going live; without it the /admin
   login won't work at all.

USING THE BLOG (day to day)
-------------------
- Go to https://probuildercrm.com/admin
- Log in with the ADMIN_PASSWORD you set
- "+ New Post" — fill in Title, Category, Author, Date, Read time, a
  short Excerpt (shown on the blog list page), and the post content
  itself (add Paragraph / Heading / List blocks as needed)
- "Save Post" — it appears on /blog immediately, no rebuild needed
- Edit or Delete any post the same way, any time

AFTER IT'S LIVE
-------------------
- Go to Google Search Console (search.google.com/search-console),
  add the probuildercrm.com property, and submit
  https://probuildercrm.com/sitemap.xml
- Every blog post you add is automatically included in the sitemap
