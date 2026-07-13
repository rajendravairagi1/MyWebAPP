"use client";

export default function Dialog({ open, title, children, onClose }) {
  if (!open) return null;
  return (
    <div
      style={{
        position: "fixed",
        inset: 0,
        background: "rgba(26,26,26,0.5)",
        display: "flex",
        alignItems: "center",
        justifyContent: "center",
        zIndex: 100,
        padding: "var(--space-md)",
      }}
      onClick={onClose}
    >
      <div
        onClick={(e) => e.stopPropagation()}
        style={{
          background: "var(--color-bg)",
          borderRadius: "var(--radius-lg)",
          boxShadow: "var(--shadow-xl)",
          padding: "var(--space-lg)",
          maxWidth: "480px",
          width: "100%",
          fontFamily: "var(--font-sans)",
          maxHeight: "90vh",
          overflowY: "auto",
        }}
      >
        <div style={{ display: "flex", justifyContent: "space-between", alignItems: "center", marginBottom: "var(--space-md)" }}>
          <h3 style={{ font: "var(--font-h4)", margin: 0, color: "var(--color-text-primary)" }}>{title}</h3>
          <button
            onClick={onClose}
            aria-label="Close dialog"
            style={{ background: "none", border: "none", fontSize: "20px", cursor: "pointer", color: "var(--color-text-secondary)" }}
          >
            ×
          </button>
        </div>
        {children}
      </div>
    </div>
  );
}
