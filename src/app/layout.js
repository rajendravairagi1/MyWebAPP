import "./globals.css";
import Navbar from "@/components/layout/Navbar";
import Footer from "@/components/layout/Footer";
import FloatingContactButtons from "@/components/layout/FloatingContactButtons";
import { SITE, NAV_ITEMS, FOOTER_COLUMNS } from "@/data/site";

export const metadata = {
  metadataBase: new URL(SITE.url),
  title: {
    default: `${SITE.name} — ${SITE.tagline}`,
    template: `%s | ${SITE.name}`,
  },
  description:
    "Oneweblink runs SEO, paid, and content programs for ambitious B2B teams across the US, UK, Australia, Canada, UAE and New Zealand — built for pipeline, not vanity rank checks.",
  openGraph: {
    title: `${SITE.name} — ${SITE.tagline}`,
    description: "Global SEO, local search, paid and content programs built for pipeline.",
    url: SITE.url,
    siteName: SITE.name,
    type: "website",
  },
};

export default function RootLayout({ children }) {
  return (
    <html lang="en">
      <body>
        <Navbar brand={SITE.name} items={NAV_ITEMS} ctaLabel="Book a Consultation" ctaHref="/contact" />
        {children}
        <Footer brand={SITE.name} columns={FOOTER_COLUMNS} copyright={`© ${new Date().getFullYear()} ${SITE.legalName}. All rights reserved.`} />
        <FloatingContactButtons phone={SITE.phoneHref} whatsapp={SITE.whatsapp} />
      </body>
    </html>
  );
}
