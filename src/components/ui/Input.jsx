"use client";

export default function Input({ label, type = "text", placeholder, required = false, error, name, value, onChange, ...rest }) {
  return (
    <label style={{ display: "flex", flexDirection: "column", gap: "6px", fontFamily: "var(--font-sans)" }}>
      {label && (
        <span style={{ font: "var(--font-label)", color: "var(--color-text-primary)" }}>
          {label}
          {required && <span style={{ color: "var(--color-danger)" }}> *</span>}
        </span>
      )}
      <input
        type={type}
        name={name}
        placeholder={placeholder}
        required={required}
        value={value}
        onChange={onChange}
        style={{
          font: "var(--font-body)",
          padding: "12px 14px",
          minHeight: "var(--min-tap)",
          borderRadius: "var(--radius-sm)",
          border: `1px solid ${error ? "var(--color-danger)" : "var(--color-border)"}`,
          outline: "none",
          background: "var(--color-bg)",
          color: "var(--color-text-primary)",
          transition: "border-color var(--transition-fast), box-shadow var(--transition-fast)",
          boxSizing: "border-box",
        }}
        onFocus={(e) => {
          e.currentTarget.style.borderColor = "var(--color-accent)";
          e.currentTarget.style.boxShadow = "0 0 0 3px var(--color-accent-tint)";
        }}
        onBlur={(e) => {
          e.currentTarget.style.borderColor = error ? "var(--color-danger)" : "var(--color-border)";
          e.currentTarget.style.boxShadow = "none";
        }}
        {...rest}
      />
      {error && <span style={{ font: "var(--font-body-sm)", color: "var(--color-danger)" }}>{error}</span>}
    </label>
  );
}
