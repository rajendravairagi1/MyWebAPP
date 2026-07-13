"use client";

import { useState } from "react";
import Icon from "./ui/Icon";

export default function FaqAccordion({ faqs }) {
  const [open, setOpen] = useState(0);

  return (
    <div style={{ maxWidth: "760px", margin: "0 auto" }}>
      {faqs.map((f, i) => (
        <div key={f.q} style={{ borderBottom: "1px solid var(--color-border)" }}>
          <button
            onClick={() => setOpen(open === i ? -1 : i)}
            style={{
              width: "100%",
              background: "none",
              border: "none",
              cursor: "pointer",
              display: "flex",
              alignItems: "flex-start",
              justifyContent: "space-between",
              gap: "var(--space-sm)",
              padding: "var(--space-md) 0",
              textAlign: "left",
              fontFamily: "var(--font-sans)",
            }}
          >
            <span style={{ font: "var(--font-h4)", color: "var(--color-text-primary)" }}>{f.q}</span>
            <Icon name={open === i ? "minus" : "plus"} size={20} style={{ color: "var(--color-accent-dark)", flexShrink: 0, marginTop: "4px" }} />
          </button>
          {open === i && <p style={{ font: "var(--font-body)", color: "var(--color-text-secondary)", margin: "0 0 var(--space-md)" }}>{f.a}</p>}
        </div>
      ))}
    </div>
  );
}
