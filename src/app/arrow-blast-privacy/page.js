export const metadata = {
  title: "Arrow Blast — Privacy Policy",
  description: "Privacy policy for the Arrow Blast mobile game.",
};

const wrap = { fontFamily: "Arial, sans-serif", maxWidth: 760, margin: "0 auto", padding: "48px 20px", color: "#222", lineHeight: 1.6 };
const h1 = { fontSize: 28, fontWeight: 900, marginBottom: 4 };
const h2 = { fontSize: 18, fontWeight: 800, marginTop: 32, marginBottom: 8 };
const p = { fontSize: 15, color: "#444", marginBottom: 8 };

export default function ArrowBlastPrivacyPage() {
  const updated = "July 22, 2026";
  return (
    <div style={wrap}>
      <h1 style={h1}>Arrow Blast — Privacy Policy</h1>
      <p style={{ ...p, color: "#888" }}>Last updated: {updated}</p>

      <h2 style={h2}>Overview</h2>
      <p style={p}>
        Arrow Blast is a single-player puzzle game. This policy explains what information the app
        handles and how.
      </p>

      <h2 style={h2}>Information we collect</h2>
      <p style={p}>
        Arrow Blast does not collect, transmit, or store any personal information on any server.
        The app does not require sign-up, does not ask for your name, email, location, contacts,
        or any other personal data, and does not use analytics or tracking SDKs.
      </p>

      <h2 style={h2}>Data stored on your device</h2>
      <p style={p}>
        The app saves your progress — level stars, your best scores, and which levels are
        unlocked — using local storage on your own device only. This data never leaves your
        device, is never sent to us or any third party, and is deleted automatically if you
        uninstall the app or clear the app&apos;s storage from your phone&apos;s settings.
      </p>

      <h2 style={h2}>Permissions</h2>
      <p style={p}>
        The app may request vibration access to give haptic feedback when you tap. This
        permission is not used to access any personal data.
      </p>

      <h2 style={h2}>Third parties</h2>
      <p style={p}>
        Arrow Blast does not integrate any third-party advertising, analytics, or data-sharing
        services. No information is shared with anyone.
      </p>

      <h2 style={h2}>Children&apos;s privacy</h2>
      <p style={p}>
        Arrow Blast does not knowingly collect any information from anyone, including children,
        because it does not collect information from any user at all.
      </p>

      <h2 style={h2}>Changes to this policy</h2>
      <p style={p}>
        If this policy changes, the updated version will be posted on this page with a new
        &ldquo;Last updated&rdquo; date.
      </p>

      <h2 style={h2}>Contact</h2>
      <p style={p}>
        Questions about this policy can be sent to{" "}
        <a href="mailto:Info@oneweblink.com">Info@oneweblink.com</a>.
      </p>
    </div>
  );
}
