"use client";

export default function Tabs({ tabs = [], active = 0, onChange }) {
  return (
    <div style={{ display: "flex", gap: "var(--space-lg)", borderBottom: "1px solid var(--color-border)", fontFamily: "var(--font-sans)" }}>
      {tabs.map((t, i) => (
        <button
          key={t}
          onClick={() => onChange && onChange(i)}
          style={{
            background: "none",
            border: "none",
            borderBottom: i === active ? "2px solid var(--color-accent)" : "2px solid transparent",
            color: i === active ? "var(--color-text-primary)" : "var(--color-text-secondary)",
            font: i === active ? "var(--font-label)" : "var(--font-body-sm)",
            padding: "10px 2px",
            cursor: "pointer",
            transition: "color var(--transition-fast), border-color var(--transition-fast)",
          }}
        >
          {t}
        </button>
      ))}
    </div>
  );
}
