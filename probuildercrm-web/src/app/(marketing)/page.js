import Link from "next/link";
import Tag from "@/components/ui/Tag";
import JsonLd from "@/components/seo/JsonLd";
import { faqSchema } from "@/lib/schema";
import { CORE_FEATURES, TEAM_FEATURES } from "@/data/features";
import { FAQS } from "@/data/faq";
import { SITE } from "@/data/site";

export const metadata = {
  title: "Real Estate & Construction CRM Software for Builders",
  description: SITE.shortDescription,
  alternates: { canonical: "/" },
};

export default function HomePage() {
  const topFaqs = FAQS.slice(0, 4);

  return (
    <>
      <JsonLd data={faqSchema(topFaqs)} />

      {/* Hero */}
      <section style={{ background: "var(--color-bg-inverse)", padding: "var(--space-3xl) 0", textAlign: "center" }}>
        <div className="container" style={{ maxWidth: 820 }}>
          <p style={{ font: "var(--font-eyebrow)", color: "#a5b4fc", textTransform: "uppercase", letterSpacing: "0.08em", margin: "0 0 12px" }}>
            Built for Real Estate Builders &amp; Developers
          </p>
          <h1 style={{ font: "var(--font-h1)", color: "#fff", margin: "0 0 var(--space-md)" }}>
            Run your entire real estate business from one CRM — not five spreadsheets.
          </h1>
          <p style={{ font: "var(--font-body-lg)", color: "var(--gray-300)", margin: "0 auto var(--space-lg)", maxWidth: 640 }}>
            Projects, unit bookings, customer payments, loans, invoices, contractors and brokers — Pro Builder CRM keeps every
            number in one place, so you always know exactly where your business stands.
          </p>
          <div style={{ display: "flex", gap: 12, justifyContent: "center", flexWrap: "wrap" }}>
            <Link href="/contact" className="btn btn-primary btn-lg">
              Book a Free Demo
            </Link>
            <Link href="/features" className="btn btn-secondary btn-lg" style={{ background: "transparent", color: "#fff", borderColor: "rgba(255,255,255,0.25)" }}>
              See Features
            </Link>
          </div>
        </div>
      </section>

      {/* Core features */}
      <section className="section">
        <div className="container">
          <div style={{ textAlign: "center", maxWidth: 640, margin: "0 auto var(--space-xl)" }}>
            <Tag>What it does</Tag>
            <h2 style={{ font: "var(--font-h2)", margin: "12px 0 0" }}>Everything a builder actually needs, nothing they don&apos;t</h2>
          </div>

          <div style={{ display: "flex", flexDirection: "column", gap: "var(--space-lg)" }}>
            {CORE_FEATURES.map((feature) => (
              <div key={feature.title} className="card" style={{ display: "flex", flexDirection: "column", gap: 8 }}>
                <h3 style={{ font: "var(--font-h3)", margin: 0 }}>{feature.title}</h3>
                <p style={{ margin: 0, color: "var(--color-ink-soft)" }}>{feature.description}</p>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Team features grid */}
      <section className="section" style={{ background: "var(--color-bg-soft)" }}>
        <div className="container">
          <div style={{ textAlign: "center", maxWidth: 640, margin: "0 auto var(--space-xl)" }}>
            <Tag>Built for the whole team</Tag>
            <h2 style={{ font: "var(--font-h2)", margin: "12px 0 0" }}>Not just for the owner</h2>
          </div>

          <div className="grid-3">
            {TEAM_FEATURES.map((feature) => (
              <div key={feature.title} className="card">
                <h3 style={{ font: "var(--font-h3)", margin: "0 0 8px" }}>{feature.title}</h3>
                <p style={{ margin: 0, color: "var(--color-ink-soft)", fontSize: "0.95rem" }}>{feature.description}</p>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Regions */}
      <section className="section-tight" style={{ textAlign: "center" }}>
        <div className="container">
          <p style={{ color: "var(--color-ink-soft)", fontWeight: 600 }}>Trusted by builders in</p>
          <div style={{ display: "flex", justifyContent: "center", gap: 24, flexWrap: "wrap", marginTop: 12, fontWeight: 700, color: "var(--color-ink)" }}>
            {SITE.regions.map((region) => (
              <span key={region}>{region}</span>
            ))}
          </div>
        </div>
      </section>

      {/* CTA */}
      <section className="section" style={{ background: "var(--color-primary)", color: "#fff", textAlign: "center" }}>
        <div className="container" style={{ maxWidth: 620 }}>
          <h2 style={{ font: "var(--font-h2)", margin: "0 0 var(--space-sm)", color: "#fff" }}>
            Let&apos;s set it up around your actual projects
          </h2>
          <p style={{ color: "#e0e7ff", margin: "0 0 var(--space-lg)" }}>
            Book a free demo and we&apos;ll walk you through setting up your first project live.
          </p>
          <Link href="/contact" className="btn btn-lg" style={{ background: "#fff", color: "var(--color-primary)" }}>
            Book a Free Demo
          </Link>
        </div>
      </section>
    </>
  );
}
