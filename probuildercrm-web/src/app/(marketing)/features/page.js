import Link from "next/link";
import Tag from "@/components/ui/Tag";
import { CORE_FEATURES, TEAM_FEATURES } from "@/data/features";

export const metadata = {
  title: "Features",
  description:
    "See everything Pro Builder CRM handles — project & unit management, customer payments, loan disbursements, invoices, contractors, brokers, investors and more.",
  alternates: { canonical: "/features" },
};

export default function FeaturesPage() {
  return (
    <>
      <section style={{ background: "var(--color-bg-inverse)", padding: "var(--space-3xl) 0", textAlign: "center" }}>
        <div className="container" style={{ maxWidth: 720 }}>
          <p style={{ font: "var(--font-eyebrow)", color: "#a5b4fc", textTransform: "uppercase", letterSpacing: "0.08em", margin: "0 0 12px" }}>
            Features
          </p>
          <h1 style={{ font: "var(--font-h1)", color: "#fff", margin: "0 0 var(--space-sm)" }}>
            Every project, every unit, always up to date
          </h1>
          <p style={{ font: "var(--font-body-lg)", color: "var(--gray-300)", margin: 0 }}>
            One system for the whole real estate business — not a spreadsheet for each piece of it.
          </p>
        </div>
      </section>

      <section className="section">
        <div className="container" style={{ display: "flex", flexDirection: "column", gap: "var(--space-lg)" }}>
          {CORE_FEATURES.map((feature) => (
            <div key={feature.title} className="card">
              <h2 style={{ font: "var(--font-h2)", fontSize: "1.4rem", margin: "0 0 8px" }}>{feature.title}</h2>
              <p style={{ margin: 0, color: "var(--color-ink-soft)" }}>{feature.description}</p>
            </div>
          ))}
        </div>
      </section>

      <section className="section" style={{ background: "var(--color-bg-soft)" }}>
        <div className="container">
          <div style={{ textAlign: "center", maxWidth: 640, margin: "0 auto var(--space-xl)" }}>
            <Tag>Built for the whole team</Tag>
            <h2 style={{ font: "var(--font-h2)", margin: "12px 0 0" }}>Not just for you</h2>
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

      <section className="section-tight" style={{ textAlign: "center" }}>
        <div className="container">
          <h2 style={{ font: "var(--font-h2)", margin: "0 0 var(--space-md)" }}>Let&apos;s set it up around your actual projects</h2>
          <Link href="/contact" className="btn btn-primary btn-lg">
            Book a Free Demo
          </Link>
        </div>
      </section>
    </>
  );
}
