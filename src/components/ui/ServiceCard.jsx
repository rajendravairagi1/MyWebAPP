"use client";

import Icon from "./Icon";

export default function ServiceCard({ number, title, icon, description, bullets }) {
  return (
    <div
      style={{
        border: "1px solid var(--color-border)",
        borderRadius: "var(--radius-lg)",
        padding: "var(--space-lg)",
        background: "var(--color-bg)",
        boxShadow: "var(--shadow-sm)",
        display: "flex",
        flexDirection: "column",
        gap: "var(--space-md)",
        transition: "transform var(--transition-fast), box-shadow var(--transition-fast)",
      }}
      onMouseEnter={(e) => {
        e.currentTarget.style.transform = "translateY(-4px)";
        e.currentTarget.style.boxShadow = "var(--shadow-lg)";
      }}
      onMouseLeave={(e) => {
        e.currentTarget.style.transform = "none";
        e.currentTarget.style.boxShadow = "var(--shadow-sm)";
      }}
    >
      <div style={{ display: "flex", alignItems: "center", justifyContent: "space-between" }}>
        <div
          style={{
            width: "52px",
            height: "52px",
            borderRadius: "var(--radius-md)",
            background: "var(--color-accent-tint-weak)",
            display: "flex",
            alignItems: "center",
            justifyContent: "center",
          }}
        >
          <Icon name={icon} size={24} style={{ color: "var(--color-accent-dark)" }} />
        </div>
        <span style={{ font: "var(--font-eyebrow)", color: "var(--color-text-secondary)" }}>/{number}</span>
      </div>
      <div>
        <h3 style={{ font: "var(--font-h3)", color: "var(--color-text-primary)", margin: "0 0 8px" }}>{title}</h3>
        <p style={{ font: "var(--font-body)", color: "var(--color-text-secondary)", margin: 0 }}>{description}</p>
      </div>
      {bullets && (
        <div
          style={{
            display: "grid",
            gridTemplateColumns: "repeat(2, 1fr)",
            gap: "var(--space-md)",
            borderTop: "1px solid var(--color-border)",
            paddingTop: "var(--space-md)",
          }}
        >
          {bullets.map((b) => (
            <div key={b.h}>
              <div style={{ display: "flex", alignItems: "center", gap: "6px", marginBottom: "4px" }}>
                <Icon name="chevron-right" size={14} style={{ color: "var(--color-accent-dark)", flexShrink: 0 }} />
                <span style={{ font: "var(--font-label)", color: "var(--color-text-primary)" }}>{b.h}</span>
              </div>
              <p style={{ font: "var(--font-body-sm)", color: "var(--color-text-secondary)", margin: 0, paddingLeft: "20px" }}>
                {b.d}
              </p>
            </div>
          ))}
        </div>
      )}
    </div>
  );
}
