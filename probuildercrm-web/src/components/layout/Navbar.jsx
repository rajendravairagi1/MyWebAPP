"use client";

import { useState } from "react";
import Link from "next/link";
import Logo from "./Logo";

export default function Navbar({ items, ctaLabel, ctaHref }) {
  const [open, setOpen] = useState(false);

  return (
    <header
      style={{
        position: "sticky",
        top: 0,
        zIndex: 40,
        background: "rgba(255,255,255,0.9)",
        backdropFilter: "saturate(180%) blur(8px)",
        borderBottom: "1px solid var(--color-border)",
      }}
    >
      <div
        className="container"
        style={{ display: "flex", alignItems: "center", justifyContent: "space-between", height: 72 }}
      >
        <Link href="/" style={{ textDecoration: "none" }}>
          <Logo />
        </Link>

        <nav style={{ display: "flex", alignItems: "center", gap: 28 }} className="nav-desktop">
          {items.map((item) => (
            <Link
              key={item.href}
              href={item.href}
              style={{ textDecoration: "none", color: "var(--color-ink-soft)", fontWeight: 500, fontSize: "0.95rem" }}
            >
              {item.label}
            </Link>
          ))}
          <Link href={ctaHref} className="btn btn-primary">
            {ctaLabel}
          </Link>
        </nav>

        <button
          type="button"
          aria-label="Toggle menu"
          onClick={() => setOpen((v) => !v)}
          className="nav-toggle"
          style={{
            display: "none",
            background: "none",
            border: "none",
            padding: 8,
            cursor: "pointer",
          }}
        >
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
            <path strokeLinecap="round" strokeLinejoin="round" d="M4 6h16M4 12h16M4 18h16" />
          </svg>
        </button>
      </div>

      {open && (
        <div style={{ borderTop: "1px solid var(--color-border)", background: "#fff" }} className="nav-mobile-panel">
          <div className="container" style={{ display: "flex", flexDirection: "column", gap: 4, padding: "16px 24px" }}>
            {items.map((item) => (
              <Link
                key={item.href}
                href={item.href}
                onClick={() => setOpen(false)}
                style={{ textDecoration: "none", color: "var(--color-ink)", padding: "10px 0", fontWeight: 500 }}
              >
                {item.label}
              </Link>
            ))}
            <Link href={ctaHref} onClick={() => setOpen(false)} className="btn btn-primary" style={{ marginTop: 8, width: "100%" }}>
              {ctaLabel}
            </Link>
          </div>
        </div>
      )}

      <style>{`
        @media (max-width: 860px) {
          .nav-desktop { display: none !important; }
          .nav-toggle { display: inline-flex !important; }
        }
      `}</style>
    </header>
  );
}
