import Link from "next/link";
import { notFound } from "next/navigation";
import Icon from "@/components/ui/Icon";
import Button from "@/components/ui/Button";
import { SERVICES, getServiceBySlug } from "@/data/services";

export function generateStaticParams() {
  return SERVICES.map((s) => ({ slug: s.slug }));
}

export async function generateMetadata({ params }) {
  const { slug } = await params;
  const service = getServiceBySlug(slug);
  if (!service) return {};
  return {
    title: service.title,
    description: service.description,
  };
}

export default async function ServiceDetailPage({ params }) {
  const { slug } = await params;
  const service = getServiceBySlug(slug);
  if (!service) notFound();

  const jsonLd = {
    "@context": "https://schema.org",
    "@type": "Service",
    name: service.title,
    description: service.longDescription,
    provider: { "@type": "Organization", name: "Oneweblink Pvt Ltd" },
  };

  const otherServices = SERVICES.filter((s) => s.slug !== service.slug);

  return (
    <div style={{ fontFamily: "var(--font-sans)" }}>
      <script type="application/ld+json" dangerouslySetInnerHTML={{ __html: JSON.stringify(jsonLd) }} />

      <section style={{ background: "var(--color-bg-inverse)", padding: "var(--space-3xl) var(--container-padding)" }}>
        <div style={{ maxWidth: "var(--container-max)", margin: "0 auto" }}>
          <Link href="/services" style={{ font: "var(--font-label)", color: "var(--blue-500)", textDecoration: "none" }}>
            &larr; All Services
          </Link>
          <div style={{ display: "flex", alignItems: "center", gap: "var(--space-md)", margin: "var(--space-md) 0" }}>
            <div
              style={{
                width: "56px",
                height: "56px",
                borderRadius: "var(--radius-md)",
                background: "rgba(59,130,246,0.15)",
                display: "flex",
                alignItems: "center",
                justifyContent: "center",
                flexShrink: 0,
              }}
            >
              <Icon name={service.icon} size={28} style={{ color: "var(--blue-500)" }} />
            </div>
            <span style={{ font: "var(--font-eyebrow)", color: "var(--blue-500)" }}>/{service.number}</span>
          </div>
          <h1 style={{ font: "var(--font-h1)", color: "var(--white)", margin: "0 0 var(--space-sm)", maxWidth: "760px" }}>{service.title}</h1>
          <p style={{ font: "var(--font-body-lg)", color: "var(--gray-300)", margin: 0, maxWidth: "640px" }}>{service.longDescription}</p>
        </div>
      </section>

      <section style={{ padding: "var(--space-3xl) 0" }}>
        <div
          className="grid-2"
          style={{ maxWidth: "var(--container-max)", margin: "0 auto", padding: "0 var(--container-padding)", display: "grid", gridTemplateColumns: "1fr 1fr", gap: "var(--space-2xl)" }}
        >
          <div>
            <h2 style={{ font: "var(--font-h3)", color: "var(--color-text-primary)", margin: "0 0 var(--space-md)" }}>What&rsquo;s included</h2>
            <div style={{ display: "flex", flexDirection: "column", gap: "var(--space-lg)" }}>
              {service.bullets.map((b) => (
                <div key={b.h} style={{ display: "flex", gap: "12px" }}>
                  <Icon name="check-circle-2" size={20} style={{ color: "var(--color-success)", flexShrink: 0, marginTop: "2px" }} />
                  <div>
                    <h3 style={{ font: "var(--font-h4)", color: "var(--color-text-primary)", margin: "0 0 4px" }}>{b.h}</h3>
                    <p style={{ font: "var(--font-body)", color: "var(--color-text-secondary)", margin: 0 }}>{b.d}</p>
                  </div>
                </div>
              ))}
            </div>
          </div>
          <div>
            <h2 style={{ font: "var(--font-h3)", color: "var(--color-text-primary)", margin: "0 0 var(--space-md)" }}>Deliverables</h2>
            <ul style={{ listStyle: "none", padding: 0, margin: "0 0 var(--space-lg)", display: "flex", flexDirection: "column", gap: "10px" }}>
              {service.deliverables.map((d) => (
                <li key={d} style={{ display: "flex", alignItems: "center", gap: "8px", font: "var(--font-body)", color: "var(--color-text-primary)" }}>
                  <Icon name="check" size={16} style={{ color: "var(--color-success)", flexShrink: 0 }} />
                  {d}
                </li>
              ))}
            </ul>
            <div
              style={{
                border: "1px solid var(--color-border)",
                borderRadius: "var(--radius-lg)",
                padding: "var(--space-lg)",
                background: "var(--color-bg-subtle)",
              }}
            >
              <h3 style={{ font: "var(--font-h4)", color: "var(--color-text-primary)", margin: "0 0 8px" }}>Ready to talk strategy?</h3>
              <p style={{ font: "var(--font-body-sm)", color: "var(--color-text-secondary)", margin: "0 0 var(--space-md)" }}>
                Every engagement starts with a free audit — no obligation.
              </p>
              <Button variant="primary" href="/contact">
                Book a Free Consultation
              </Button>
            </div>
          </div>
        </div>
      </section>

      <section style={{ background: "var(--color-bg-subtle)", padding: "var(--space-2xl) 0" }}>
        <div style={{ maxWidth: "var(--container-max)", margin: "0 auto", padding: "0 var(--container-padding)" }}>
          <h2 style={{ font: "var(--font-h3)", color: "var(--color-text-primary)", margin: "0 0 var(--space-md)" }}>Explore other services</h2>
          <div style={{ display: "flex", gap: "var(--space-sm)", flexWrap: "wrap" }}>
            {otherServices.map((s) => (
              <Link
                key={s.slug}
                href={`/services/${s.slug}`}
                style={{
                  display: "inline-flex",
                  alignItems: "center",
                  gap: "8px",
                  padding: "10px 18px",
                  border: "1px solid var(--color-border)",
                  borderRadius: "var(--radius-pill)",
                  background: "var(--color-bg)",
                  color: "var(--color-text-primary)",
                  textDecoration: "none",
                  font: "var(--font-label)",
                }}
              >
                <Icon name={s.icon} size={16} style={{ color: "var(--color-accent-dark)" }} />
                {s.title}
              </Link>
            ))}
          </div>
        </div>
      </section>
    </div>
  );
}
