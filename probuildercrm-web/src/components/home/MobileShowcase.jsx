export default function MobileShowcase() {
  return (
    <div
      style={{
        position: "relative",
        width: 260,
        margin: "0 auto",
        borderRadius: 36,
        border: "10px solid #111827",
        background: "#111827",
        boxShadow: "var(--shadow-lg)",
        overflow: "hidden",
      }}
    >
      <div
        style={{
          position: "absolute",
          top: 0,
          left: "50%",
          transform: "translateX(-50%)",
          width: 90,
          height: 20,
          background: "#111827",
          borderBottomLeftRadius: 14,
          borderBottomRightRadius: 14,
          zIndex: 2,
        }}
      />
      {/* eslint-disable-next-line @next/next/no-img-element */}
      <img
        src="/screenshots/mobile-dashboard.png"
        alt="Pro Builder CRM dashboard open on a mobile phone, showing project stats and payment collection charts"
        style={{ width: "100%", display: "block", borderRadius: 26 }}
      />
    </div>
  );
}
