import { SITE } from "@/data/site";

export const metadata = {
  title: "Privacy Policy",
  robots: { index: true, follow: true },
  alternates: { canonical: "/privacy-policy" },
};

export default function PrivacyPolicyPage() {
  return (
    <section className="section">
      <div className="container" style={{ maxWidth: 760 }}>
        <h1 style={{ font: "var(--font-h1)", fontSize: "2rem" }}>Privacy Policy</h1>
        <p style={{ color: "var(--color-ink-soft)" }}>Last updated: {new Date().getFullYear()}</p>

        <div style={{ display: "flex", flexDirection: "column", gap: "var(--space-md)", color: "var(--color-ink-soft)", lineHeight: 1.7 }}>
          <p>
            {SITE.legalName} (&quot;we&quot;, &quot;us&quot;) operates {SITE.name}. This policy explains what information we
            collect when you use our website and product, and how we use it.
          </p>
          <h2 style={{ font: "var(--font-h3)", color: "var(--color-ink)" }}>Information we collect</h2>
          <p>
            When you fill out our contact form or book a demo, we collect your name, email address, phone number and any
            message you send us. When you use Pro Builder CRM itself, the business data you enter (projects, customers,
            payments, etc.) is stored to provide the service to you.
          </p>
          <h2 style={{ font: "var(--font-h3)", color: "var(--color-ink)" }}>How we use it</h2>
          <p>
            We use this information to respond to your enquiries, provide the CRM service, and improve our product. We do
            not sell your information to third parties.
          </p>
          <h2 style={{ font: "var(--font-h3)", color: "var(--color-ink)" }}>Data security</h2>
          <p>
            Every business&apos;s data on Pro Builder CRM is isolated from every other business, and passwords are stored
            using one-way industry-standard hashing.
          </p>
          <h2 style={{ font: "var(--font-h3)", color: "var(--color-ink)" }}>Contact</h2>
          <p>
            Questions about this policy? Email us at <a href={`mailto:${SITE.email}`}>{SITE.email}</a>.
          </p>
        </div>
      </div>
    </section>
  );
}
