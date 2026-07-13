"use client";

export default function Card({ children, padded = true, style: styleOverride }) {
  return (
    <div
      style={{
        background: "var(--color-bg)",
        border: "1px solid var(--color-border)",
        borderRadius: "var(--radius-md)",
        padding: padded ? "var(--space-md)" : 0,
        boxShadow: "var(--shadow-sm)",
        transition: "box-shadow var(--transition-fast), transform var(--transition-fast)",
        ...styleOverride,
      }}
      onMouseEnter={(e) => {
        e.currentTarget.style.boxShadow = "var(--shadow-lg)";
        e.currentTarget.style.transform = "translateY(-2px)";
      }}
      onMouseLeave={(e) => {
        e.currentTarget.style.boxShadow = "var(--shadow-sm)";
        e.currentTarget.style.transform = "none";
      }}
    >
      {children}
    </div>
  );
}
