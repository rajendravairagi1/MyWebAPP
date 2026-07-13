"use client";

export default function Textarea({ label, placeholder, required = false, rows = 5, name, value, onChange, ...rest }) {
  return (
    <label style={{ display: "flex", flexDirection: "column", gap: "6px", fontFamily: "var(--font-sans)" }}>
      {label && (
        <span style={{ font: "var(--font-label)", color: "var(--color-text-primary)" }}>
          {label}
          {required && <span style={{ color: "var(--color-danger)" }}> *</span>}
        </span>
      )}
      <textarea
        name={name}
        placeholder={placeholder}
        required={required}
        rows={rows}
        value={value}
        onChange={onChange}
        style={{
          font: "var(--font-body)",
          padding: "12px 14px",
          borderRadius: "var(--radius-sm)",
          border: "1px solid var(--color-border)",
          outline: "none",
          resize: "vertical",
          background: "var(--color-bg)",
          color: "var(--color-text-primary)",
          transition: "border-color var(--transition-fast), box-shadow var(--transition-fast)",
          boxSizing: "border-box",
          fontFamily: "var(--font-sans)",
        }}
        onFocus={(e) => {
          e.currentTarget.style.borderColor = "var(--color-accent)";
          e.currentTarget.style.boxShadow = "0 0 0 3px var(--color-accent-tint)";
        }}
        onBlur={(e) => {
          e.currentTarget.style.borderColor = "var(--color-border)";
          e.currentTarget.style.boxShadow = "none";
        }}
        {...rest}
      />
    </label>
  );
}
