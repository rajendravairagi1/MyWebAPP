import ContactForm from "@/components/contact/ContactForm";
import { SITE } from "@/data/site";

export const metadata = {
  title: "Contact Us",
  description: "Book a free demo of Pro Builder CRM, built around your actual projects.",
  alternates: { canonical: "/contact" },
};

export default function ContactPage() {
  return (
    <>
      <section style={{ background: "var(--color-bg-inverse)", padding: "var(--space-3xl) 0", textAlign: "center" }}>
        <div className="container" style={{ maxWidth: 720 }}>
          <p style={{ font: "var(--font-eyebrow)", color: "#a5b4fc", textTransform: "uppercase", letterSpacing: "0.08em", margin: "0 0 12px" }}>
            Contact
          </p>
          <h1 style={{ font: "var(--font-h1)", color: "#fff", margin: "0 0 var(--space-sm)" }}>Let&apos;s talk about your business</h1>
          <p style={{ font: "var(--font-body-lg)", color: "var(--gray-300)", margin: 0 }}>
            Tell us a bit about your projects and we&apos;ll set up a walkthrough built around them — not a generic demo.
          </p>
        </div>
      </section>

      <section className="section">
        <div className="container" style={{ maxWidth: 960, display: "grid", gridTemplateColumns: "1fr 1.2fr", gap: "var(--space-xl)" }}>
          <div style={{ display: "flex", flexDirection: "column", gap: 16 }}>
            <a href={SITE.whatsapp} target="_blank" rel="noopener noreferrer" className="card" style={{ textDecoration: "none" }}>
              <h3 style={{ font: "var(--font-h3)", margin: "0 0 4px" }}>WhatsApp</h3>
              <p style={{ margin: 0, color: "var(--color-ink-soft)" }}>Fastest way to reach us — message us directly.</p>
            </a>
            <a href={`mailto:${SITE.email}`} className="card" style={{ textDecoration: "none" }}>
              <h3 style={{ font: "var(--font-h3)", margin: "0 0 4px" }}>Email</h3>
              <p style={{ margin: 0, color: "var(--color-ink-soft)" }}>{SITE.email}</p>
            </a>
            <div className="card">
              <h3 style={{ font: "var(--font-h3)", margin: "0 0 4px" }}>Book a Call</h3>
              <p style={{ margin: 0, color: "var(--color-ink-soft)" }}>Message us on WhatsApp or email with a good time, and we&apos;ll set up a call.</p>
            </div>
          </div>

          <ContactForm />
        </div>
      </section>
    </>
  );
}
