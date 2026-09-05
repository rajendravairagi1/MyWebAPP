export default function Tag({ children }) {
  return (
    <span
      style={{
        display: "inline-flex",
        alignItems: "center",
        padding: "4px 10px",
        borderRadius: 999,
        background: "var(--color-primary-light)",
        color: "var(--color-primary)",
        fontSize: "0.75rem",
        fontWeight: 700,
        letterSpacing: "0.02em",
        textTransform: "uppercase",
        width: "fit-content",
      }}
    >
      {children}
    </span>
  );
}
