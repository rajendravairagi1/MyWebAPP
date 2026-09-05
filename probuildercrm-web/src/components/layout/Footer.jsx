import Link from "next/link";
import Logo from "./Logo";

export default function Footer({ columns, copyright }) {
  return (
    <footer style={{ background: "var(--color-bg-inverse)", color: "var(--gray-300)", padding: "var(--space-2xl) 0 var(--space-lg)" }}>
      <div className="container">
        <div style={{ display: "grid", gridTemplateColumns: "1.4fr repeat(3, 1fr)", gap: "var(--space-lg)" }} className="footer-grid">
          <div>
            <Logo light />
            <p style={{ marginTop: 12, maxWidth: 320, color: "var(--gray-300)", fontSize: "0.9rem" }}>
              The all-in-one CRM built for real estate builders and developers.
            </p>
          </div>

          {columns.map((col) => (
            <div key={col.heading}>
              <div style={{ color: "#fff", fontWeight: 700, fontSize: "0.85rem", marginBottom: 14 }}>{col.heading}</div>
              <div style={{ display: "flex", flexDirection: "column", gap: 10 }}>
                {col.links.map((link) => (
                  <Link key={link.href} href={link.href} style={{ textDecoration: "none", color: "var(--gray-300)", fontSize: "0.9rem" }}>
                    {link.label}
                  </Link>
                ))}
              </div>
            </div>
          ))}
        </div>

        <div
          style={{
            marginTop: "var(--space-2xl)",
            paddingTop: "var(--space-md)",
            borderTop: "1px solid rgba(255,255,255,0.1)",
            fontSize: "0.85rem",
            color: "var(--gray-300)",
          }}
        >
          {copyright}
        </div>
      </div>

      <style>{`
        @media (max-width: 860px) {
          .footer-grid { grid-template-columns: 1fr 1fr !important; }
        }
      `}</style>
    </footer>
  );
}
