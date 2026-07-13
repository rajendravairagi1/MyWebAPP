import { SITE } from "@/data/site";

export const metadata = {
  title: "Terms of Service",
  description: "Terms governing use of the Oneweblink Pvt Ltd website and engagement of Oneweblink's services.",
};

const sectionStyle = { marginBottom: "var(--space-lg)" };
const headingStyle = { font: "var(--font-h3)", color: "var(--color-text-primary)", margin: "0 0 8px" };
const bodyStyle = { font: "var(--font-body)", color: "var(--color-text-secondary)", margin: "0 0 8px" };

export default function TermsOfServicePage() {
  const updated = "July 13, 2026";
  return (
    <div style={{ fontFamily: "var(--font-sans)" }}>
      <section style={{ background: "var(--color-bg-inverse)", padding: "var(--space-2xl) var(--container-padding)", textAlign: "center" }}>
        <h1 style={{ font: "var(--font-h1)", color: "var(--white)", margin: "0 0 8px" }}>Terms of Service</h1>
        <p style={{ font: "var(--font-body)", color: "var(--gray-300)", margin: 0 }}>Last updated {updated}</p>
      </section>

      <section style={{ padding: "var(--space-3xl) var(--container-padding)" }}>
        <div style={{ maxWidth: "760px", margin: "0 auto" }}>
          <div style={sectionStyle}>
            <h2 style={headingStyle}>Agreement</h2>
            <p style={bodyStyle}>
              These terms govern your use of this website and, once agreed in a separate proposal or statement of work,
              your engagement of services from Oneweblink Pvt Ltd. By using this site or engaging our services, you
              agree to these terms.
            </p>
          </div>

          <div style={sectionStyle}>
            <h2 style={headingStyle}>Services</h2>
            <p style={bodyStyle}>
              Specific deliverables, timelines, and fees for any SEO, paid media, content, or web engagement are set out
              in a separate proposal or statement of work agreed with you before work begins. Nothing on this website
              constitutes a binding offer of services.
            </p>
          </div>

          <div style={sectionStyle}>
            <h2 style={headingStyle}>No ranking guarantees</h2>
            <p style={bodyStyle}>
              Search engine rankings and algorithm behavior are outside any agency&rsquo;s direct control. We commit to
              the strategy, effort, and reporting described in your engagement, but we do not guarantee specific rankings
              or traffic outcomes.
            </p>
          </div>

          <div style={sectionStyle}>
            <h2 style={headingStyle}>Engagement terms</h2>
            <p style={bodyStyle}>
              Client engagements are month-to-month with 30 days&rsquo; written notice to cancel, unless a different term
              is agreed in writing for a specific engagement.
            </p>
          </div>

          <div style={sectionStyle}>
            <h2 style={headingStyle}>Website use</h2>
            <p style={bodyStyle}>
              Content on this website is provided for general informational purposes and may be updated at any time
              without notice. You may not reproduce or redistribute this site&rsquo;s content without permission.
            </p>
          </div>

          <div style={sectionStyle}>
            <h2 style={headingStyle}>Contact</h2>
            <p style={bodyStyle}>
              Questions about these terms can be sent to {SITE.email} or {SITE.phone}.
            </p>
          </div>
        </div>
      </section>
    </div>
  );
}
