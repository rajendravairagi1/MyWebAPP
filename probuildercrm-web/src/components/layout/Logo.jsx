/**
 * Text-based placeholder logo. Replace src/data/site.js `logo` with a real
 * image path (e.g. "/logo.png" dropped into /public) and swap this for an
 * <img> once the actual brand mark is ready — this keeps the header/footer
 * working correctly in the meantime.
 */
export default function Logo({ light = false }) {
  return (
    <span
      style={{
        display: "inline-flex",
        alignItems: "center",
        gap: 8,
        fontWeight: 800,
        fontSize: "1.15rem",
        color: light ? "#fff" : "var(--color-ink)",
        letterSpacing: "-0.01em",
      }}
    >
      <span
        style={{
          display: "inline-flex",
          alignItems: "center",
          justifyContent: "center",
          width: 30,
          height: 30,
          borderRadius: 8,
          background: "var(--color-primary)",
          color: "#fff",
          fontSize: "0.95rem",
        }}
      >
        P
      </span>
      Pro Builder <span style={{ color: "var(--color-primary)" }}>CRM</span>
    </span>
  );
}
