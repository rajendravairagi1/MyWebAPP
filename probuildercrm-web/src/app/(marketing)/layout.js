import Navbar from "@/components/layout/Navbar";
import Footer from "@/components/layout/Footer";
import FloatingWhatsApp from "@/components/layout/FloatingWhatsApp";
import JsonLd from "@/components/seo/JsonLd";
import { organizationSchema, softwareApplicationSchema } from "@/lib/schema";
import { SITE, NAV_ITEMS, FOOTER_COLUMNS } from "@/data/site";

export default function MarketingLayout({ children }) {
  return (
    <>
      <JsonLd data={[organizationSchema(), softwareApplicationSchema()]} />
      <Navbar items={NAV_ITEMS} ctaLabel="Book a Free Demo" ctaHref="/contact" />
      {children}
      <Footer columns={FOOTER_COLUMNS} copyright={`© ${new Date().getFullYear()} ${SITE.legalName}. All rights reserved.`} />
      <FloatingWhatsApp href={SITE.whatsapp} />
    </>
  );
}
