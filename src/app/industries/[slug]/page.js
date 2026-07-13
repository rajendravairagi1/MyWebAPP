import Link from "next/link";
import { notFound } from "next/navigation";
import Icon from "@/components/ui/Icon";
import Button from "@/components/ui/Button";
import { INDUSTRIES, getIndustryBySlug } from "@/data/industries";

export function generateStaticParams() {
  return INDUSTRIES.map((i) => ({ slug: i.slug }));
}

export async function generateMetadata({ params }) {
  const { slug } = await params;
  const industry = getIndustryBySlug(slug);
  if (!industry) return {};
  return {
    title: industry.title,
    description: industry.description,
  };
}

export default async function IndustryDetailPage({ params }) {
  const { slug } = await params;
  const industry = getIndustryBySlug(slug);
  if (!industry) notFound();

  const otherIndustries = INDUSTRIES.filter((i) => i.slug !== industry.slug);

  return (
    <div style={{ fontFamily: "var(--font-sans)" }}>
      <section style={{ background: "var(--color-bg-inverse)", padding: "var(--space-3xl) var(--container-padding)" }}>
        <div style={{ maxWidth: "var(--container-max)", margin: "0 auto" }}>
          <Link href="/industries" style={{ font: "var(--font-label)", color: "var(--blue-500)", textDecoration: "none" }}>
            &larr; All Industries
          </Link>
          <div
            style={{
              width: "56px",
              height: "56px",
              borderRadius: "var(--radius-md)",
              background: "rgba(59,130,246,0.15)",
              display: "flex",
              alignItems: "center",
              justifyContent: "center",
              margin: "var(--space-md) 0",
            }}
          >
            <Icon name={industry.icon} size={28} style={{ color: "var(--blue-500)" }} />
          </div>
          <h1 style={{ font: "var(--font-h1)", color: "var(--white)", margin: "0 0 var(--space-sm)", maxWidth: "760px" }}>{industry.title}</h1>
          <p style={{ font: "var(--font-body-lg)", color: "var(--gray-300)", margin: 0, maxWidth: "640px" }}>{industry.longDescription}</p>
        </div>
      </section>

      <section style={{ padding: "var(--space-3xl) 0" }}>
        <div
          className="grid-2"
          style={{ maxWidth: "var(--container-max)", margin: "0 auto", padding: "0 var(--container-padding)", display: "grid", gridTemplateColumns: "1fr 1fr", gap: "var(--space-2xl)" }}
        >
          <div>
            <h2 style={{ font: "var(--font-h3)", color: "var(--color-text-primary)", margin: "0 0 var(--space-md)" }}>Common challenges</h2>
            <ul style={{ listStyle: "none", padding: 0, margin: 0, display: "flex", flexDirection: "column", gap: "12px" }}>
              {industry.challenges.map((c) => (
                <li key={c} style={{ display: "flex", gap: "10px", font: "var(--font-body)", color: "var(--color-text-primary)" }}>
                  <Icon name="target" size={18} style={{ color: "var(--color-danger)", flexShrink: 0, marginTop: "3px" }} />
                  {c}
                </li>
              ))}
            </ul>
          </div>
          <div>
            <h2 style={{ font: "var(--font-h3)", color: "var(--color-text-primary)", margin: "0 0 var(--space-md)" }}>Our approach</h2>
            <ul style={{ listStyle: "none", padding: 0, margin: 0, display: "flex", flexDirection: "column", gap: "12px" }}>
              {industry.approach.map((a) => (
                <li key={a} style={{ display: "flex", gap: "10px", font: "var(--font-body)", color: "var(--color-text-primary)" }}>
                  <Icon name="check-circle-2" size={18} style={{ color: "var(--color-success)", flexShrink: 0, marginTop: "3px" }} />
                  {a}
                </li>
              ))}
            </ul>
          </div>
        </div>
      </section>

      <section style={{ padding: "var(--space-2xl) var(--container-padding)", textAlign: "center", background: "var(--color-accent-tint-weak)" }}>
        <h2 style={{ font: "var(--font-h2)", color: "var(--color-text-primary)", margin: "0 0 8px" }}>
          Let&rsquo;s talk about {industry.title.toLowerCase()} search strategy
        </h2>
        <p style={{ font: "var(--font-body-lg)", color: "var(--color-text-secondary)", margin: "0 0 var(--space-lg)" }}>
          Get a free audit built around what actually matters in your industry.
        </p>
        <Button variant="primary" size="lg" href="/contact">
          Book a Free Consultation
        </Button>
      </section>

      <section style={{ padding: "var(--space-2xl) 0" }}>
        <div style={{ maxWidth: "var(--container-max)", margin: "0 auto", padding: "0 var(--container-padding)" }}>
          <h2 style={{ font: "var(--font-h3)", color: "var(--color-text-primary)", margin: "0 0 var(--space-md)" }}>Other industries</h2>
          <div style={{ display: "flex", gap: "var(--space-sm)", flexWrap: "wrap" }}>
            {otherIndustries.map((i) => (
              <Link
                key={i.slug}
                href={`/industries/${i.slug}`}
                style={{
                  display: "inline-flex",
                  alignItems: "center",
                  gap: "8px",
                  padding: "10px 18px",
                  border: "1px solid var(--color-border)",
                  borderRadius: "var(--radius-pill)",
                  color: "var(--color-text-primary)",
                  textDecoration: "none",
                  font: "var(--font-label)",
                }}
              >
                <Icon name={i.icon} size={16} style={{ color: "var(--color-accent-dark)" }} />
                {i.title}
              </Link>
            ))}
          </div>
        </div>
      </section>
    </div>
  );
}
