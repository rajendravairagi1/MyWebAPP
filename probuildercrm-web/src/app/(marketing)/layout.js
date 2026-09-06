import Navbar from "@/components/layout/Navbar";
import Footer from "@/components/layout/Footer";
import FloatingWhatsApp from "@/components/layout/FloatingWhatsApp";
import JsonLd from "@/components/seo/JsonLd";
import { organizationSchema, softwareApplicationSchema } from "@/lib/schema";
import { listPlans } from "@/lib/repositories/pricing";
import { SITE, NAV_ITEMS, FOOTER_COLUMNS } from "@/data/site";

export const dynamic = "force-dynamic";

export default function MarketingLayout({ children }) {
  const plans = listPlans();
  const startingPrice = Math.min(...plans.map((p) => p.monthlyPrice));

  return (
    <>
      <JsonLd data={[organizationSchema(), softwareApplicationSchema(startingPrice)]} />
      <Navbar items={NAV_ITEMS} ctaLabel="Book a Free Demo" ctaHref="/contact" />
      {children}
      <Footer columns={FOOTER_COLUMNS} copyright={`© ${new Date().getFullYear()} ${SITE.legalName}. All rights reserved.`} />
      <FloatingWhatsApp href={SITE.whatsapp} />
    </>
  );
}
