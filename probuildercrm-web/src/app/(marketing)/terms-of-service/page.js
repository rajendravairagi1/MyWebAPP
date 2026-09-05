import { SITE } from "@/data/site";

export const metadata = {
  title: "Terms of Service",
  robots: { index: true, follow: true },
  alternates: { canonical: "/terms-of-service" },
};

export default function TermsOfServicePage() {
  return (
    <section className="section">
      <div className="container" style={{ maxWidth: 760 }}>
        <h1 style={{ font: "var(--font-h1)", fontSize: "2rem" }}>Terms of Service</h1>
        <p style={{ color: "var(--color-ink-soft)" }}>Last updated: {new Date().getFullYear()}</p>

        <div style={{ display: "flex", flexDirection: "column", gap: "var(--space-md)", color: "var(--color-ink-soft)", lineHeight: 1.7 }}>
          <p>
            By using {SITE.name}, provided by {SITE.legalName}, you agree to these terms.
          </p>
          <h2 style={{ font: "var(--font-h3)", color: "var(--color-ink)" }}>Using the service</h2>
          <p>
            Pro Builder CRM is provided on a subscription basis. You&apos;re responsible for the accuracy of the data you
            enter and for keeping your account credentials secure.
          </p>
          <h2 style={{ font: "var(--font-h3)", color: "var(--color-ink)" }}>Payments</h2>
          <p>Subscription fees are billed in advance on the plan you choose, and are non-refundable except where required by law.</p>
          <h2 style={{ font: "var(--font-h3)", color: "var(--color-ink)" }}>Your data</h2>
          <p>You own the business data you enter into Pro Builder CRM. We do not sell it or share it with third parties.</p>
          <h2 style={{ font: "var(--font-h3)", color: "var(--color-ink)" }}>Contact</h2>
          <p>
            Questions about these terms? Email us at <a href={`mailto:${SITE.email}`}>{SITE.email}</a>.
          </p>
        </div>
      </div>
    </section>
  );
}
