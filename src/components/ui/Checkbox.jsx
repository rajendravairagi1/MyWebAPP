"use client";

export default function Checkbox({ label, checked = false, onChange, name }) {
  return (
    <label style={{ display: "flex", alignItems: "center", gap: "10px", fontFamily: "var(--font-sans)", cursor: "pointer" }}>
      <input
        type="checkbox"
        name={name}
        checked={checked}
        onChange={onChange}
        style={{ width: "18px", height: "18px", accentColor: "var(--color-accent)" }}
      />
      <span style={{ font: "var(--font-body-sm)", color: "var(--color-text-primary)" }}>{label}</span>
    </label>
  );
}
