import Button from "@/components/ui/Button";

export default function NotFound() {
  return (
    <div
      style={{
        fontFamily: "var(--font-sans)",
        minHeight: "60vh",
        display: "flex",
        flexDirection: "column",
        alignItems: "center",
        justifyContent: "center",
        textAlign: "center",
        padding: "var(--space-3xl) var(--container-padding)",
        gap: "var(--space-md)",
      }}
    >
      <span style={{ font: "var(--font-eyebrow)", color: "var(--color-accent-dark)", textTransform: "uppercase", letterSpacing: "0.06em" }}>
        404
      </span>
      <h1 style={{ font: "var(--font-h1)", color: "var(--color-text-primary)", margin: 0 }}>Page not found</h1>
      <p style={{ font: "var(--font-body-lg)", color: "var(--color-text-secondary)", margin: 0, maxWidth: "480px" }}>
        The page you&rsquo;re looking for doesn&rsquo;t exist or has moved.
      </p>
      <Button variant="primary" href="/">
        Back to Home
      </Button>
    </div>
  );
}
