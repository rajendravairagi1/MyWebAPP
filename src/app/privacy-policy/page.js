import { SITE } from "@/data/site";

export const metadata = {
  title: "Privacy Policy",
  description: "How Oneweblink Pvt Ltd collects, uses, and protects information submitted through this website.",
};

const sectionStyle = { marginBottom: "var(--space-lg)" };
const headingStyle = { font: "var(--font-h3)", color: "var(--color-text-primary)", margin: "0 0 8px" };
const bodyStyle = { font: "var(--font-body)", color: "var(--color-text-secondary)", margin: "0 0 8px" };

export default function PrivacyPolicyPage() {
  const updated = "July 13, 2026";
  return (
    <div style={{ fontFamily: "var(--font-sans)" }}>
      <section style={{ background: "var(--color-bg-inverse)", padding: "var(--space-2xl) var(--container-padding)", textAlign: "center" }}>
        <h1 style={{ font: "var(--font-h1)", color: "var(--white)", margin: "0 0 8px" }}>Privacy Policy</h1>
        <p style={{ font: "var(--font-body)", color: "var(--gray-300)", margin: 0 }}>Last updated {updated}</p>
      </section>

      <section style={{ padding: "var(--space-3xl) var(--container-padding)" }}>
        <div style={{ maxWidth: "760px", margin: "0 auto" }}>
          <div style={sectionStyle}>
            <h2 style={headingStyle}>Overview</h2>
            <p style={bodyStyle}>
              Oneweblink Pvt Ltd (&ldquo;Oneweblink&rdquo;, &ldquo;we&rdquo;, &ldquo;us&rdquo;) respects your privacy. This
              policy explains what information we collect through this website, how we use it, and the choices you have.
            </p>
          </div>

          <div style={sectionStyle}>
            <h2 style={headingStyle}>Information we collect</h2>
            <p style={bodyStyle}>
              When you submit a consultation or contact form, we collect the information you provide directly — typically
              your name, email address, phone number, and the message you send. We do not collect payment information
              through this site.
            </p>
          </div>

          <div style={sectionStyle}>
            <h2 style={headingStyle}>How we use it</h2>
            <p style={bodyStyle}>
              We use the information you submit to respond to your inquiry, prepare a proposal or audit, and, if you
              become a client, to deliver our services. We do not sell your information to third parties.
            </p>
          </div>

          <div style={sectionStyle}>
            <h2 style={headingStyle}>Data retention</h2>
            <p style={bodyStyle}>
              We retain inquiry information for as long as needed to respond to you and, if you engage us, for the
              duration of our working relationship plus a reasonable period afterward for record-keeping.
            </p>
          </div>

          <div style={sectionStyle}>
            <h2 style={headingStyle}>Your choices</h2>
            <p style={bodyStyle}>
              You can request access to, correction of, or deletion of the information you&rsquo;ve submitted to us at
              any time by emailing {SITE.email}.
            </p>
          </div>

          <div style={sectionStyle}>
            <h2 style={headingStyle}>Contact</h2>
            <p style={bodyStyle}>
              Questions about this policy can be sent to {SITE.email} or {SITE.phone}.
            </p>
          </div>
        </div>
      </section>
    </div>
  );
}
