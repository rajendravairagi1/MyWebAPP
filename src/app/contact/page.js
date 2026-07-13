import Icon from "@/components/ui/Icon";
import ConsultationForm from "@/components/ConsultationForm";
import { SITE } from "@/data/site";

export const metadata = {
  title: "Contact",
  description: "Get in touch with Oneweblink for a free SEO audit and 90-day growth roadmap.",
};

export default function ContactPage() {
  return (
    <div style={{ fontFamily: "var(--font-sans)" }}>
      <section style={{ background: "var(--color-bg-inverse)", padding: "var(--space-3xl) var(--container-padding)", textAlign: "center" }}>
        <p style={{ font: "var(--font-eyebrow)", color: "var(--blue-500)", textTransform: "uppercase", letterSpacing: "0.06em", margin: "0 0 8px" }}>
          Get In Touch
        </p>
        <h1 style={{ font: "var(--font-h1)", color: "var(--white)", margin: "0 0 var(--space-sm)" }}>Let&rsquo;s talk about growth</h1>
        <p style={{ font: "var(--font-body-lg)", color: "var(--gray-300)", margin: "0 auto", maxWidth: "560px" }}>
          Tell us about your goals — we&rsquo;ll reply within one business day with next steps.
        </p>
      </section>

      <section style={{ padding: "var(--space-3xl) 0" }}>
        <div
          className="grid-2"
          style={{ maxWidth: "var(--container-max)", margin: "0 auto", padding: "0 var(--container-padding)", display: "grid", gridTemplateColumns: "0.8fr 1.2fr", gap: "var(--space-2xl)" }}
        >
          <div style={{ display: "flex", flexDirection: "column", gap: "var(--space-lg)" }}>
            <div>
              <p style={{ font: "var(--font-label)", color: "var(--color-text-secondary)", margin: "0 0 6px", display: "flex", alignItems: "center", gap: "8px" }}>
                <Icon name="phone" size={16} /> Phone
              </p>
              <a href={`tel:${SITE.phoneHref}`} style={{ font: "var(--font-h4)", color: "var(--color-accent-dark)", textDecoration: "none" }}>
                {SITE.phone}
              </a>
            </div>
            <div>
              <p style={{ font: "var(--font-label)", color: "var(--color-text-secondary)", margin: "0 0 6px", display: "flex", alignItems: "center", gap: "8px" }}>
                <Icon name="mail" size={16} /> Email
              </p>
              <a href={`mailto:${SITE.email}`} style={{ font: "var(--font-h4)", color: "var(--color-accent-dark)", textDecoration: "none" }}>
                {SITE.email}
              </a>
            </div>
            <div>
              <p style={{ font: "var(--font-label)", color: "var(--color-text-secondary)", margin: "0 0 6px", display: "flex", alignItems: "center", gap: "8px" }}>
                <Icon name="map-pin" size={16} /> Address
              </p>
              <p style={{ font: "var(--font-body)", color: "var(--color-text-primary)", margin: 0 }}>{SITE.address}</p>
            </div>
            <a
              href={SITE.whatsapp}
              target="_blank"
              rel="noopener noreferrer"
              style={{
                display: "inline-flex",
                alignItems: "center",
                gap: "8px",
                padding: "12px 22px",
                borderRadius: "var(--radius-sm)",
                background: "var(--color-whatsapp)",
                color: "var(--white)",
                textDecoration: "none",
                font: "var(--weight-semibold) var(--text-base)/1 var(--font-sans)",
                width: "fit-content",
              }}
            >
              <Icon name="message-circle" size={18} /> Chat on WhatsApp
            </a>
            <p style={{ font: "var(--font-body-sm)", color: "var(--color-text-secondary)", margin: 0 }}>
              We usually respond within one business day.
            </p>
          </div>

          <div
            style={{
              background: "var(--color-bg)",
              border: "1px solid var(--color-border)",
              borderRadius: "var(--radius-lg)",
              padding: "var(--space-lg)",
              boxShadow: "var(--shadow-sm)",
            }}
          >
            <ConsultationForm source="contact-page" />
          </div>
        </div>
      </section>
    </div>
  );
}
