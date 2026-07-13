"use client";

const tones = {
  success: { background: "var(--color-success-bg)", color: "var(--color-success)", border: "var(--color-success)" },
  danger: { background: "var(--color-danger-bg)", color: "var(--color-danger)", border: "var(--color-danger)" },
};

export default function Toast({ message, tone = "success", onClose }) {
  const t = tones[tone];
  return (
    <div
      style={{
        display: "flex",
        alignItems: "center",
        justifyContent: "space-between",
        gap: "16px",
        padding: "14px 18px",
        borderRadius: "var(--radius-md)",
        background: t.background,
        color: t.color,
        border: `1px solid ${t.border}`,
        font: "var(--font-body-sm)",
        fontFamily: "var(--font-sans)",
        boxShadow: "var(--shadow-md)",
      }}
    >
      <span>{message}</span>
      {onClose && (
        <button
          onClick={onClose}
          style={{ background: "none", border: "none", color: "inherit", cursor: "pointer", fontSize: "16px", lineHeight: 1 }}
        >
          ×
        </button>
      )}
    </div>
  );
}
