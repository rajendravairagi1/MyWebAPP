import Link from "next/link";

export const metadata = {
  title: "About Us",
  description: "Why Pro Builder CRM exists, and who it's built for.",
  alternates: { canonical: "/about" },
};

export default function AboutPage() {
  return (
    <>
      <section style={{ background: "var(--color-bg-inverse)", padding: "var(--space-3xl) 0", textAlign: "center" }}>
        <div className="container" style={{ maxWidth: 760 }}>
          <p style={{ font: "var(--font-eyebrow)", color: "#a5b4fc", textTransform: "uppercase", letterSpacing: "0.08em", margin: "0 0 12px" }}>
            About Us
          </p>
          <h1 style={{ font: "var(--font-h1)", color: "#fff", margin: "0 0 var(--space-sm)" }}>
            Built by people who&apos;ve watched builders fight with Excel for years
          </h1>
          <p style={{ font: "var(--font-body-lg)", color: "var(--gray-300)", margin: 0 }}>
            Pro Builder CRM exists because real estate builders deserve software built around how they actually work — not a
            generic CRM stretched to fit.
          </p>
        </div>
      </section>

      <section className="section">
        <div className="container" style={{ maxWidth: 760, display: "flex", flexDirection: "column", gap: "var(--space-md)" }}>
          <h2 style={{ font: "var(--font-h2)" }}>Why we built this</h2>
          <p style={{ color: "var(--color-ink-soft)", fontSize: "1.05rem" }}>
            Every builder we spoke to had the same story: bookings tracked in one Excel file, payments in another, site
            photos scattered across phones, and a WhatsApp group standing in for a proper follow-up system. One missed
            installment or one lost paper cost real money — and a customer&apos;s trust.
          </p>
          <p style={{ color: "var(--color-ink-soft)", fontSize: "1.05rem" }}>
            Pro Builder CRM was built to be the single place all of that lives — projects, units, customers, payments,
            loans, invoices, contractors, brokers and investors — simple enough to set up in a day, and built specifically
            around a builder&apos;s actual day-to-day.
          </p>
        </div>
      </section>

      <section className="section" style={{ background: "var(--color-primary)", color: "#fff", textAlign: "center" }}>
        <div className="container" style={{ maxWidth: 620 }}>
          <h2 style={{ font: "var(--font-h2)", color: "#fff", margin: "0 0 var(--space-sm)" }}>Let&apos;s talk about your business</h2>
          <p style={{ color: "#e0e7ff", margin: "0 0 var(--space-lg)" }}>
            Tell us a bit about your projects and we&apos;ll set up a walkthrough built around them — not a generic demo.
          </p>
          <Link href="/contact" className="btn btn-lg" style={{ background: "#fff", color: "var(--color-primary)" }}>
            Get in Touch
          </Link>
        </div>
      </section>
    </>
  );
}
