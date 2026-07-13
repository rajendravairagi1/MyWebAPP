"use client";

import { useState } from "react";
import Link from "next/link";
import Icon from "../ui/Icon";
import Button from "../ui/Button";

export default function Navbar({ brand, items = [], ctaLabel = "Book a Consultation", ctaHref = "/contact" }) {
  const [open, setOpen] = useState(false);

  return (
    <header
      style={{
        position: "sticky",
        top: 0,
        zIndex: 40,
        background: "rgba(255,255,255,0.9)",
        backdropFilter: "blur(8px)",
        borderBottom: "1px solid var(--color-border)",
        fontFamily: "var(--font-sans)",
      }}
    >
      <div
        style={{
          maxWidth: "var(--container-max)",
          margin: "0 auto",
          padding: "16px var(--container-padding)",
          display: "flex",
          alignItems: "center",
          justifyContent: "space-between",
        }}
      >
        <Link
          href="/"
          style={{
            font: "var(--weight-bold) var(--text-xl)/1 var(--font-sans)",
            color: "var(--color-text-primary)",
            letterSpacing: "var(--tracking-tight)",
            textDecoration: "none",
          }}
        >
          {brand}
        </Link>

        <nav className="ds-nav-links" style={{ display: "flex", gap: "var(--space-lg)" }}>
          {items.map((it) => (
            <Link
              key={it.label}
              href={it.href}
              style={{ font: "var(--font-label)", color: "var(--color-text-secondary)", textDecoration: "none" }}
            >
              {it.label}
            </Link>
          ))}
        </nav>

        <div style={{ display: "flex", alignItems: "center", gap: "var(--space-sm)" }}>
          <div className="ds-nav-links">
            <Button variant="primary" href={ctaHref}>
              {ctaLabel}
            </Button>
          </div>
          <button
            onClick={() => setOpen(!open)}
            aria-label="Toggle menu"
            className="ds-nav-toggle"
            style={{ display: "none", background: "none", border: "none", cursor: "pointer" }}
          >
            <Icon name={open ? "x" : "menu"} size={24} style={{ color: "var(--color-text-primary)" }} />
          </button>
        </div>
      </div>

      {open && (
        <div
          style={{
            borderTop: "1px solid var(--color-border)",
            padding: "var(--space-md)",
            display: "flex",
            flexDirection: "column",
            gap: "var(--space-md)",
            background: "var(--color-bg)",
          }}
        >
          {items.map((it) => (
            <Link
              key={it.label}
              href={it.href}
              onClick={() => setOpen(false)}
              style={{ font: "var(--font-label)", color: "var(--color-text-primary)", textDecoration: "none" }}
            >
              {it.label}
            </Link>
          ))}
          <Button variant="primary" href={ctaHref} onClick={() => setOpen(false)}>
            {ctaLabel}
          </Button>
        </div>
      )}
    </header>
  );
}
