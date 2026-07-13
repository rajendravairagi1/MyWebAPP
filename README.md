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
- `src/app/api/contact` — route handler backing the consultation/contact forms.
- `src/components/ui` — design system primitives (Button, Input, Dialog, Card, etc.), ported from the synced Oneweblink design system.
- `src/components/layout` — Navbar, Footer, FloatingContactButtons.
- `src/data` — site content (services, industries, blog posts, case studies, FAQs) — swap for a CMS/DB later without touching page markup.
- `src/styles/tokens` — color, typography, and spacing CSS custom properties from the design system.

## Scripts

- `npm run dev` — start the dev server
- `npm run build` — production build
- `npm run start` — run the production build
- `npm run lint` — lint the project
