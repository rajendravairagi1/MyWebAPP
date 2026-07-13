import Link from "next/link";
import Icon from "../ui/Icon";
import { SITE } from "@/data/site";

export default function Footer({ brand, columns = [], copyright }) {
  const socialLinks = [
    { label: "LinkedIn", icon: "linkedin", href: SITE.social.linkedin },
    { label: "Instagram", icon: "instagram", href: SITE.social.instagram },
    { label: "Facebook", icon: "facebook", href: SITE.social.facebook },
  ];

  return (
    <footer style={{ background: "var(--color-bg-inverse)", color: "var(--gray-300)", fontFamily: "var(--font-sans)" }}>
      <div
        style={{
          maxWidth: "var(--container-max)",
          margin: "0 auto",
          padding: "var(--space-2xl) var(--container-padding) var(--space-lg)",
          display: "grid",
          gridTemplateColumns: "1.4fr repeat(4, 1fr)",
          gap: "var(--space-lg)",
        }}
        className="grid-4"
      >
        <div>
          <span style={{ font: "var(--weight-bold) var(--text-xl)/1 var(--font-sans)", color: "var(--white)" }}>{brand}</span>
          <p style={{ font: "var(--font-body-sm)", color: "var(--gray-300)", margin: "var(--space-sm) 0 0", maxWidth: "260px" }}>
            SEO, paid, and content programs for ambitious teams across the US, UK, Australia, Canada, UAE and New Zealand.
          </p>
          <div style={{ display: "flex", gap: "10px", marginTop: "var(--space-md)" }}>
            {socialLinks.map((s) => (
              <a
                key={s.label}
                href={s.href}
                target={s.href !== "#" ? "_blank" : undefined}
                rel={s.href !== "#" ? "noopener noreferrer" : undefined}
                aria-label={s.label}
                style={{
                  width: "36px",
                  height: "36px",
                  borderRadius: "50%",
                  border: "1px solid rgba(255,255,255,0.25)",
                  display: "flex",
                  alignItems: "center",
                  justifyContent: "center",
                  color: "var(--white)",
                }}
              >
                <Icon name={s.icon} size={16} />
              </a>
            ))}
          </div>
        </div>
        {columns.map((col) => (
          <div key={col.heading} style={{ display: "flex", flexDirection: "column", gap: "12px" }}>
            <span style={{ font: "var(--font-label)", color: "var(--white)" }}>{col.heading}</span>
            {col.links.map((l) => (
              <Link key={l.label} href={l.href} style={{ font: "var(--font-body-sm)", color: "var(--gray-300)", textDecoration: "none" }}>
                {l.label}
              </Link>
            ))}
          </div>
        ))}
      </div>
      <div
        style={{
          borderTop: "1px solid rgba(255,255,255,0.1)",
          padding: "var(--space-md) var(--container-padding)",
          maxWidth: "var(--container-max)",
          margin: "0 auto",
          display: "flex",
          alignItems: "center",
          justifyContent: "space-between",
          flexWrap: "wrap",
          gap: "var(--space-sm)",
        }}
      >
        <span style={{ font: "var(--font-body-sm)", color: "var(--gray-600)" }}>{copyright}</span>
        <div style={{ display: "flex", gap: "var(--space-md)" }}>
          <Link href="/privacy-policy" style={{ font: "var(--font-body-sm)", color: "var(--gray-600)", textDecoration: "none" }}>
            Privacy Policy
          </Link>
          <Link href="/terms-of-service" style={{ font: "var(--font-body-sm)", color: "var(--gray-600)", textDecoration: "none" }}>
            Terms of Service
          </Link>
        </div>
      </div>
    </footer>
  );
}
