"use client";

export default function Switch({ label, checked = false, onChange }) {
  return (
    <label style={{ display: "inline-flex", alignItems: "center", gap: "10px", cursor: "pointer", fontFamily: "var(--font-sans)" }}>
      <span
        onClick={() => onChange && onChange({ target: { checked: !checked } })}
        style={{
          width: "42px",
          height: "24px",
          borderRadius: "var(--radius-pill)",
          background: checked ? "var(--color-accent)" : "var(--gray-300)",
          position: "relative",
          transition: "background var(--transition-fast)",
          flexShrink: 0,
        }}
      >
        <span
          style={{
            position: "absolute",
            top: "3px",
            left: checked ? "21px" : "3px",
            width: "18px",
            height: "18px",
            borderRadius: "50%",
            background: "var(--white)",
            boxShadow: "var(--shadow-sm)",
            transition: "left var(--transition-fast)",
          }}
        />
      </span>
      {label && <span style={{ font: "var(--font-body-sm)", color: "var(--color-text-primary)" }}>{label}</span>}
    </label>
  );
}
