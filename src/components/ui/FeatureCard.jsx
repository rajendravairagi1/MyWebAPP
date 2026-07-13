import Icon from "./Icon";

export default function FeatureCard({ icon, title, description }) {
  return (
    <div style={{ display: "flex", flexDirection: "column", gap: "14px", fontFamily: "var(--font-sans)" }}>
      <div
        style={{
          width: "48px",
          height: "48px",
          borderRadius: "var(--radius-md)",
          background: "var(--color-accent-tint-weak)",
          display: "flex",
          alignItems: "center",
          justifyContent: "center",
        }}
      >
        <Icon name={icon} size={22} style={{ color: "var(--color-accent-dark)" }} />
      </div>
      <h3 style={{ font: "var(--font-h4)", color: "var(--color-text-primary)", margin: 0 }}>{title}</h3>
      <p style={{ font: "var(--font-body-sm)", color: "var(--color-text-secondary)", margin: 0 }}>{description}</p>
    </div>
  );
}
