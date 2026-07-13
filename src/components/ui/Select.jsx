"use client";

export default function Select
({ label, options = [], name, value, onChange, required = false }) {
  return (
    <label style={{ display: "flex", flexDirection: "column", gap: "6px", fontFamily: "var(--font-sans)" }}>
      {label && <span style={{ font: "var(--font-label)", color: "var(--color-text-primary)" }}>{label}</span>}
      <select
        name={name}
        value={value}
        onChange={onChange}
        required={required}
        style={{
          font: "var(--font-body)",
          padding: "12px 14px",
          minHeight: "var(--min-tap)",
          borderRadius: "var(--radius-sm)",
          border: "1px solid var(--color-border)",
          background: "var(--color-bg)",
          color: "var(--color-text-primary)",
          outline: "none",
          boxSizing: "border-box",
        }}
      >
        {options.map((o) => (
          <option key={o.value} value={o.value}>
            {o.label}
          </option>
        ))}
      </select>
    </label>
  );
}
