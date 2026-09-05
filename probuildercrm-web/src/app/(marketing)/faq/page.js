import JsonLd from "@/components/seo/JsonLd";
import FaqAccordion from "@/components/ui/FaqAccordion";
import { faqSchema } from "@/lib/schema";
import { FAQS } from "@/data/faq";

export const metadata = {
  title: "Frequently Asked Questions",
  description: "Answers to common questions about Pro Builder CRM — setup, security, team access, pricing and more.",
  alternates: { canonical: "/faq" },
};

export default function FaqPage() {
  return (
    <>
      <JsonLd data={faqSchema(FAQS)} />

      <section style={{ background: "var(--color-bg-inverse)", padding: "var(--space-3xl) 0", textAlign: "center" }}>
        <div className="container" style={{ maxWidth: 720 }}>
          <p style={{ font: "var(--font-eyebrow)", color: "#a5b4fc", textTransform: "uppercase", letterSpacing: "0.08em", margin: "0 0 12px" }}>
            FAQ
          </p>
          <h1 style={{ font: "var(--font-h1)", color: "#fff", margin: 0 }}>Frequently Asked Questions</h1>
        </div>
      </section>

      <section className="section">
        <div className="container" style={{ maxWidth: 760 }}>
          <FaqAccordion items={FAQS} />
        </div>
      </section>
    </>
  );
}
