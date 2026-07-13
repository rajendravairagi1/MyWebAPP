const tones = {
  neutral: { background: "var(--gray-100)", color: "var(--color-text-secondary)" },
  accent: { background: "var(--color-accent-tint)", color: "var(--color-accent-dark)" },
  success: { background: "var(--color-success-bg)", color: "var(--color-success)" },
  danger: { background: "var(--color-danger-bg)", color: "var(--color-danger)" },
  inverse: { background: "rgba(255,255,255,0.1)", color: "var(--white)" },
};

export default function Badge({ children, tone = "neutral" }) {
  return (
    <span
      style={{
        display: "inline-flex",
        alignItems: "center",
        padding: "4px 12px",
        borderRadius: "var(--radius-pill)",
        font: "var(--font-eyebrow)",
        fontFamily: "var(--font-sans)",
        ...tones[tone],
      }}
    >
      {children}
    </span>
  );
}
