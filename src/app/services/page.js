import Link from "next/link";
import Button from "@/components/ui/Button";
import ServiceCard from "@/components/ui/ServiceCard";
import { SERVICES } from "@/data/services";

export const metadata = {
  title: "Growth Services",
  description: "SEO & content marketing, local search & reputation, paid & social growth, and web & emerging search — four pillars, eleven disciplines.",
};

export default function ServicesPage() {
  return (
    <div style={{ fontFamily: "var(--font-sans)" }}>
      <section style={{ background: "var(--color-bg-inverse)", padding: "var(--space-3xl) var(--container-padding)", textAlign: "center" }}>
        <p style={{ font: "var(--font-eyebrow)", color: "var(--blue-500)", textTransform: "uppercase", letterSpacing: "0.06em", margin: "0 0 8px" }}>
          What We Do
        </p>
        <h1 style={{ font: "var(--font-h1)", color: "var(--white)", margin: "0 0 var(--space-sm)" }}>Our Growth Services</h1>
        <p style={{ font: "var(--font-body-lg)", color: "var(--gray-300)", margin: "0 auto", maxWidth: "640px" }}>
          Four pillars, eleven disciplines — every engagement starts with a free audit to find the right mix for your goals.
        </p>
      </section>

      <section style={{ padding: "var(--space-3xl) 0" }}>
        <div className="grid-2" style={{ maxWidth: "var(--container-max)", margin: "0 auto", padding: "0 var(--container-padding)", display: "grid", gridTemplateColumns: "repeat(2, 1fr)", gap: "var(--space-lg)" }}>
          {SERVICES.map((s) => (
            <Link key={s.slug} href={`/services/${s.slug}`} style={{ textDecoration: "none", color: "inherit" }}>
              <ServiceCard {...s} />
            </Link>
          ))}
        </div>
      </section>

      <section style={{ padding: "var(--space-2xl) var(--container-padding)", textAlign: "center", background: "var(--color-bg-subtle)" }}>
        <h2 style={{ font: "var(--font-h2)", color: "var(--color-text-primary)", margin: "0 0 8px" }}>Not sure which service you need?</h2>
        <p style={{ font: "var(--font-body-lg)", color: "var(--color-text-secondary)", margin: "0 0 var(--space-lg)" }}>
          Every engagement starts with a free audit — we recommend only what moves your specific goals.
        </p>
        <Button variant="primary" size="lg" href="/contact">
          Book a Free Consultation
        </Button>
      </section>
    </div>
  );
}
