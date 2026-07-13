import FaqAccordion from "@/components/FaqAccordion";
import Button from "@/components/ui/Button";
import { FAQS } from "@/data/testimonials";

export const metadata = {
  title: "FAQ",
  description: "Answers to common questions about timelines, contracts, reporting, and how Oneweblink works with international clients.",
};

export default function FaqPage() {
  const jsonLd = {
    "@context": "https://schema.org",
    "@type": "FAQPage",
    mainEntity: FAQS.map((f) => ({
      "@type": "Question",
      name: f.q,
      acceptedAnswer: { "@type": "Answer", text: f.a },
    })),
  };

  return (
    <div style={{ fontFamily: "var(--font-sans)" }}>
      <script type="application/ld+json" dangerouslySetInnerHTML={{ __html: JSON.stringify(jsonLd) }} />

      <section style={{ background: "var(--color-bg-inverse)", padding: "var(--space-3xl) var(--container-padding)", textAlign: "center" }}>
        <p style={{ font: "var(--font-eyebrow)", color: "var(--blue-500)", textTransform: "uppercase", letterSpacing: "0.06em", margin: "0 0 8px" }}>
          FAQ
        </p>
        <h1 style={{ font: "var(--font-h1)", color: "var(--white)", margin: 0 }}>Questions, answered</h1>
      </section>

      <section style={{ padding: "var(--space-3xl) var(--container-padding)" }}>
        <FaqAccordion faqs={FAQS} />
      </section>

      <section style={{ padding: "var(--space-2xl) var(--container-padding)", textAlign: "center", background: "var(--color-bg-subtle)" }}>
        <h2 style={{ font: "var(--font-h2)", color: "var(--color-text-primary)", margin: "0 0 8px" }}>Still have questions?</h2>
        <Button variant="primary" size="lg" href="/contact">
          Get in Touch
        </Button>
      </section>
    </div>
  );
}
