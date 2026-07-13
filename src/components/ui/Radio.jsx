"use client";

export default function Radio({ name, options = [], value, onChange }) {
  return (
    <div style={{ display: "flex", flexDirection: "column", gap: "10px", fontFamily: "var(--font-sans)" }}>
      {options.map((o) => (
        <label key={o.value} style={{ display: "flex", alignItems: "center", gap: "10px", cursor: "pointer" }}>
          <input
            type="radio"
            name={name}
            value={o.value}
            checked={value === o.value}
            onChange={onChange}
            style={{ width: "18px", height: "18px", accentColor: "var(--color-accent)" }}
          />
          <span style={{ font: "var(--font-body-sm)", color: "var(--color-text-primary)" }}>{o.label}</span>
        </label>
      ))}
    </div>
  );
}
