import Link from "next/link";
import Icon from "@/components/ui/Icon";
import Button from "@/components/ui/Button";
import { listCaseStudies } from "@/lib/repositories/caseStudies";

export const metadata = {
  title: "Case Studies",
  description: "Real results for real clients — organic traffic growth, map pack rankings, and pipeline built through search.",
};

export const dynamic = "force-dynamic";

export default function PortfolioPage() {
  const caseStudies = listCaseStudies();
  return (
    <div style={{ fontFamily: "var(--font-sans)" }}>
      <section style={{ background: "var(--color-bg-inverse)", padding: "var(--space-3xl) var(--container-padding)", textAlign: "center" }}>
        <p style={{ font: "var(--font-eyebrow)", color: "var(--blue-500)", textTransform: "uppercase", letterSpacing: "0.06em", margin: "0 0 8px" }}>
          Our Work
        </p>
        <h1 style={{ font: "var(--font-h1)", color: "var(--white)", margin: "0 0 var(--space-sm)" }}>Don&rsquo;t take our word for it</h1>
        <p style={{ font: "var(--font-body-lg)", color: "var(--gray-300)", margin: "0 auto", maxWidth: "640px" }}>
          A sample of the programs we&rsquo;ve run for clients across SaaS, healthcare, and professional services.
        </p>
      </section>

      <section style={{ padding: "var(--space-3xl) 0" }}>
        <div style={{ maxWidth: "var(--container-max)", margin: "0 auto", padding: "0 var(--container-padding)", display: "flex", flexDirection: "column", gap: "var(--space-lg)" }}>
          {caseStudies.map((c) => (
            <Link
              key={c.slug}
              href={`/portfolio/${c.slug}`}
              style={{
                display: "grid",
                gridTemplateColumns: "1fr auto",
                gap: "var(--space-lg)",
                alignItems: "center",
                border: "1px solid var(--color-border)",
                borderRadius: "var(--radius-lg)",
                padding: "var(--space-lg)",
                textDecoration: "none",
                color: "inherit",
              }}
              className="grid-2"
            >
              <div>
                <span style={{ font: "var(--font-eyebrow)", color: "var(--color-accent-dark)", textTransform: "uppercase" }}>
                  {c.industry} &middot; {c.location}
                </span>
                <h2 style={{ font: "var(--font-h3)", color: "var(--color-text-primary)", margin: "6px 0" }}>{c.title}</h2>
                <p style={{ font: "var(--font-body)", color: "var(--color-text-secondary)", margin: "0 0 var(--space-sm)" }}>{c.summary}</p>
                <div style={{ display: "flex", gap: "var(--space-lg)", flexWrap: "wrap" }}>
                  {c.stats.map((s) => (
                    <div key={s.label}>
                      <div style={{ font: "var(--weight-bold) var(--text-2xl)/1 var(--font-sans)", color: "var(--color-accent-dark)" }}>{s.value}</div>
                      <div style={{ font: "var(--font-body-sm)", color: "var(--color-text-secondary)" }}>{s.label}</div>
                    </div>
                  ))}
                </div>
              </div>
              <Icon name="arrow-up-right" size={22} style={{ color: "var(--color-accent-dark)" }} />
            </Link>
          ))}
        </div>
      </section>

      <section style={{ padding: "var(--space-2xl) var(--container-padding)", textAlign: "center", background: "var(--color-bg-subtle)" }}>
        <h2 style={{ font: "var(--font-h2)", color: "var(--color-text-primary)", margin: "0 0 8px" }}>Want results like these?</h2>
        <Button variant="primary" size="lg" href="/contact">
          Book a Free Consultation
        </Button>
      </section>
    </div>
  );
}
