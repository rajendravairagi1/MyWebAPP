import Link from "next/link";
import { notFound } from "next/navigation";
import Button from "@/components/ui/Button";
import { listCaseStudies, getCaseStudyBySlug } from "@/lib/repositories/caseStudies";

export const dynamic = "force-dynamic";

export async function generateMetadata({ params }) {
  const { slug } = await params;
  const study = getCaseStudyBySlug(slug);
  if (!study) return {};
  return {
    title: study.title,
    description: study.summary,
  };
}

export default async function CaseStudyPage({ params }) {
  const { slug } = await params;
  const study = getCaseStudyBySlug(slug);
  if (!study) notFound();

  const others = listCaseStudies().filter((c) => c.slug !== study.slug);

  return (
    <div style={{ fontFamily: "var(--font-sans)" }}>
      <section style={{ background: "var(--color-bg-inverse)", padding: "var(--space-3xl) var(--container-padding)" }}>
        <div style={{ maxWidth: "var(--container-max)", margin: "0 auto" }}>
          <Link href="/portfolio" style={{ font: "var(--font-label)", color: "var(--blue-500)", textDecoration: "none" }}>
            &larr; All Case Studies
          </Link>
          <p style={{ font: "var(--font-eyebrow)", color: "var(--blue-500)", textTransform: "uppercase", letterSpacing: "0.06em", margin: "var(--space-md) 0 8px" }}>
            {study.industry} &middot; {study.location}
          </p>
          <h1 style={{ font: "var(--font-h1)", color: "var(--white)", margin: "0 0 var(--space-sm)", maxWidth: "780px" }}>{study.title}</h1>
          <p style={{ font: "var(--font-body-lg)", color: "var(--gray-300)", margin: 0, maxWidth: "640px" }}>{study.summary}</p>
        </div>
      </section>

      <section style={{ padding: "var(--space-2xl) 0", background: "var(--color-accent-tint-weak)" }}>
        <div
          className="grid-3"
          style={{ maxWidth: "var(--container-max)", margin: "0 auto", padding: "0 var(--container-padding)", display: "grid", gridTemplateColumns: "repeat(3, 1fr)", gap: "var(--space-lg)" }}
        >
          {study.stats.map((s) => (
            <div key={s.label} style={{ textAlign: "center" }}>
              <div style={{ font: "var(--weight-bold) var(--text-4xl)/1 var(--font-sans)", color: "var(--color-accent-dark)" }}>{s.value}</div>
              <div style={{ font: "var(--font-body-sm)", color: "var(--color-text-secondary)" }}>{s.label}</div>
            </div>
          ))}
        </div>
      </section>

      <section style={{ padding: "var(--space-3xl) 0" }}>
        <div style={{ maxWidth: "760px", margin: "0 auto", padding: "0 var(--container-padding)", display: "flex", flexDirection: "column", gap: "var(--space-xl)" }}>
          <div>
            <h2 style={{ font: "var(--font-h3)", color: "var(--color-text-primary)", margin: "0 0 8px" }}>The challenge</h2>
            <p style={{ font: "var(--font-body-lg)", color: "var(--color-text-secondary)", margin: 0 }}>{study.challenge}</p>
          </div>
          <div>
            <h2 style={{ font: "var(--font-h3)", color: "var(--color-text-primary)", margin: "0 0 8px" }}>Our approach</h2>
            <p style={{ font: "var(--font-body-lg)", color: "var(--color-text-secondary)", margin: 0 }}>{study.approach}</p>
          </div>
          <div>
            <h2 style={{ font: "var(--font-h3)", color: "var(--color-text-primary)", margin: "0 0 8px" }}>The outcome</h2>
            <p style={{ font: "var(--font-body-lg)", color: "var(--color-text-secondary)", margin: 0 }}>{study.outcome}</p>
          </div>
        </div>
      </section>

      <section style={{ padding: "var(--space-2xl) var(--container-padding)", textAlign: "center", background: "var(--color-bg-subtle)" }}>
        <h2 style={{ font: "var(--font-h2)", color: "var(--color-text-primary)", margin: "0 0 8px" }}>Want results like {study.client}&rsquo;s?</h2>
        <Button variant="primary" size="lg" href="/contact">
          Book a Free Consultation
        </Button>
      </section>

      <section style={{ padding: "var(--space-2xl) 0" }}>
        <div style={{ maxWidth: "var(--container-max)", margin: "0 auto", padding: "0 var(--container-padding)" }}>
          <h2 style={{ font: "var(--font-h3)", color: "var(--color-text-primary)", margin: "0 0 var(--space-md)" }}>More case studies</h2>
          <div style={{ display: "flex", gap: "var(--space-sm)", flexWrap: "wrap" }}>
            {others.map((c) => (
              <Link
                key={c.slug}
                href={`/portfolio/${c.slug}`}
                style={{
                  padding: "10px 18px",
                  border: "1px solid var(--color-border)",
                  borderRadius: "var(--radius-pill)",
                  color: "var(--color-text-primary)",
                  textDecoration: "none",
                  font: "var(--font-label)",
                }}
              >
                {c.client}
              </Link>
            ))}
          </div>
        </div>
      </section>
    </div>
  );
}
