import Link from "next/link";

export default function NotFound() {
  return (
    <div className="container section" style={{ textAlign: "center" }}>
      <h1 style={{ font: "var(--font-h1)" }}>Page not found</h1>
      <p style={{ color: "var(--color-ink-soft)", margin: "12px 0 var(--space-lg)" }}>
        The page you&apos;re looking for doesn&apos;t exist or has moved.
      </p>
      <Link href="/" className="btn btn-primary">
        Back to Home
      </Link>
    </div>
  );
}
