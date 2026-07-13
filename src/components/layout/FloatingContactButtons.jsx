import Icon from "../ui/Icon";

export default function FloatingContactButtons({ phone, whatsapp }) {
  const btnStyle = (bg) => ({
    width: "56px",
    height: "56px",
    borderRadius: "50%",
    display: "flex",
    alignItems: "center",
    justifyContent: "center",
    background: bg,
    color: "var(--white)",
    boxShadow: "var(--shadow-lg)",
    textDecoration: "none",
  });

  return (
    <div
      style={{
        position: "fixed",
        right: "var(--space-md)",
        bottom: "var(--space-md)",
        display: "flex",
        flexDirection: "column",
        gap: "12px",
        zIndex: 50,
      }}
    >
      <a href={whatsapp} target="_blank" rel="noopener noreferrer" aria-label="Chat on WhatsApp" style={btnStyle("var(--color-whatsapp)")}>
        <Icon name="message-circle" size={26} />
      </a>
      <a href={`tel:${phone}`} aria-label="Call us" style={btnStyle("var(--color-accent)")}>
        <Icon name="phone" size={24} />
      </a>
    </div>
  );
}
