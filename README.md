# Oneweblink

Marketing website for Oneweblink Pvt Ltd, built with Next.js (App Router). Implements the "Oneweblink Homepage" design synced from Claude Design, plus a full multi-page site: services, industries, about, portfolio, blog, contact, and FAQ.

## Getting Started

```bash
npm install
npm run dev
```

Open [http://localhost:3000](http://localhost:3000).

## Structure

- `src/app` — pages (App Router). Dynamic routes: `services/[slug]`, `industries/[slug]`, `blog/[slug]`, `portfolio/[slug]`.
- `src/app/api/contact` — route handler backing the consultation/contact forms (see Email delivery below).
- `src/app/admin` — token-gated panel for creating/editing/deleting blog posts and case studies (see Admin panel below).
- `src/components/ui` — design system primitives (Button, Input, Dialog, Card, etc.), ported from the synced Oneweblink design system.
- `src/components/layout` — Navbar, Footer, FloatingContactButtons.
- `src/data` — content for services, industries, and FAQs, plus the seed content for the database (see below).
- `src/lib/db.js` — SQLite connection (Node's built-in `node:sqlite`), schema, and one-time seeding from `src/data/blog.js` / `src/data/portfolio.js`.
- `src/lib/repositories` — read/write data access for blog posts and case studies.
- `src/lib/actions/admin.js` — server actions backing the admin panel (token-checked, call `revalidatePath` so edits show up immediately).
- `src/styles/tokens` — color, typography, and spacing CSS custom properties from the design system.

## Blog & case study storage

Blog posts and case studies are stored in a local SQLite database at `data/app.db` (gitignored, created and seeded automatically from `src/data/blog.js` / `src/data/portfolio.js` on first run). This keeps the site genuinely dynamic — content can be edited via `/admin` without a redeploy. `data/app.db` is a plain file, so on a serverless host with an ephemeral/read-only filesystem (e.g. Vercel) it won't persist across deploys; for that kind of deployment, swap `src/lib/db.js` for a hosted database (Postgres, Turso, etc.) instead.

## Admin panel

Visit `/admin` to manage blog posts and case studies. Set `ADMIN_TOKEN` in the environment to enable it — without it, all admin edits are refused. The panel is token-gated only (no real auth), so treat the token like a password and don't reuse a token from anywhere else.

## Email delivery

The contact/consultation forms POST to `/api/contact`. Set `RESEND_API_KEY` (see `.env.example`) to send real email via [Resend](https://resend.com); without it, submissions are still validated and logged server-side.

## Scripts

- `npm run dev` — start the dev server
- `npm run build` — production build
- `npm run start` — run the production build
- `npm run lint` — lint the project
