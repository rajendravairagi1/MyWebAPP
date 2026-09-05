import Link from "next/link";

export default function AdminLayout({ children }) {
  return (
    <div>
      <header style={{ borderBottom: "1px solid var(--color-border)", padding: "16px 0" }}>
        <div className="container" style={{ display: "flex", alignItems: "center", justifyContent: "space-between" }}>
          <span style={{ fontWeight: 700 }}>Pro Builder CRM — Admin</span>
          <Link href="/" style={{ fontSize: "0.9rem", color: "var(--color-ink-soft)", textDecoration: "none" }}>
            ← Back to site
          </Link>
        </div>
      </header>
      {children}
    </div>
  );
}
