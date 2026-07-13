export default function Tag({ children }) {
  return (
    <span
      style={{
        display: "inline-flex",
        alignItems: "center",
        padding: "4px 10px",
        borderRadius: "var(--radius-sm)",
        border: "1px solid var(--color-border)",
        font: "var(--weight-medium) var(--text-xs)/var(--leading-normal) var(--font-sans)",
        color: "var(--color-text-secondary)",
        background: "var(--color-bg)",
      }}
    >
      {children}
    </span>
  );
}
