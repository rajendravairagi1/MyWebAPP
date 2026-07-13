import Link from "next/link";
import Icon from "@/components/ui/Icon";
import Button from "@/components/ui/Button";
import { INDUSTRIES } from "@/data/industries";

export const metadata = {
  title: "Industries We Serve",
  description: "E-commerce, healthcare, legal, SaaS, and real estate — search strategy tuned to how each industry actually converts.",
};

export default function IndustriesPage() {
  return (
    <div style={{ fontFamily: "var(--font-sans)" }}>
      <section style={{ background: "var(--color-bg-inverse)", padding: "var(--space-3xl) var(--container-padding)", textAlign: "center" }}>
        <p style={{ font: "var(--font-eyebrow)", color: "var(--blue-500)", textTransform: "uppercase", letterSpacing: "0.06em", margin: "0 0 8px" }}>
          Who We Serve
        </p>
        <h1 style={{ font: "var(--font-h1)", color: "var(--white)", margin: "0 0 var(--space-sm)" }}>Industries We Serve Internationally</h1>
        <p style={{ font: "var(--font-body-lg)", color: "var(--gray-300)", margin: "0 auto", maxWidth: "640px" }}>
          Different industries need different search strategy. Here&rsquo;s where we go deep.
        </p>
      </section>

      <section style={{ padding: "var(--space-3xl) 0" }}>
        <div
          className="grid-3"
          style={{ maxWidth: "var(--container-max)", margin: "0 auto", padding: "0 var(--container-padding)", display: "grid", gridTemplateColumns: "repeat(3, 1fr)", gap: "var(--space-lg)" }}
        >
          {INDUSTRIES.map((i) => (
            <Link
              key={i.slug}
              href={`/industries/${i.slug}`}
              style={{
                border: "1px solid var(--color-border)",
                borderRadius: "var(--radius-lg)",
                padding: "var(--space-lg)",
                display: "flex",
                flexDirection: "column",
                gap: "var(--space-sm)",
                background: "var(--color-bg)",
                textDecoration: "none",
              }}
            >
              <div
                style={{
                  width: "48px",
                  height: "48px",
                  borderRadius: "50%",
                  background: "var(--color-accent-tint-weak)",
                  display: "flex",
                  alignItems: "center",
                  justifyContent: "center",
                }}
              >
                <Icon name={i.icon} size={22} style={{ color: "var(--color-accent-dark)" }} />
              </div>
              <h2 style={{ font: "var(--font-h4)", color: "var(--color-text-primary)", margin: 0 }}>{i.title}</h2>
              <p style={{ font: "var(--font-body-sm)", color: "var(--color-text-secondary)", margin: 0 }}>{i.description}</p>
              <span style={{ font: "var(--font-label)", color: "var(--color-accent-dark)", display: "flex", alignItems: "center", gap: "6px", marginTop: "auto" }}>
                Learn more <Icon name="arrow-up-right" size={14} />
              </span>
            </Link>
          ))}
        </div>
      </section>

      <section style={{ padding: "var(--space-2xl) var(--container-padding)", textAlign: "center", background: "var(--color-bg-subtle)" }}>
        <h2 style={{ font: "var(--font-h2)", color: "var(--color-text-primary)", margin: "0 0 8px" }}>Don&rsquo;t see your industry?</h2>
        <p style={{ font: "var(--font-body-lg)", color: "var(--color-text-secondary)", margin: "0 0 var(--space-lg)" }}>
          We build custom search strategy for teams outside these five verticals too.
        </p>
        <Button variant="primary" size="lg" href="/contact">
          Book a Free Consultation
        </Button>
      </section>
    </div>
  );
}
