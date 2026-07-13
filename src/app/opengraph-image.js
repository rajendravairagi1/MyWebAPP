import { ImageResponse } from "next/og";
import { SITE } from "@/data/site";

export const size = { width: 1200, height: 630 };
export const contentType = "image/png";

export default function OpengraphImage() {
  return new ImageResponse(
    (
      <div
        style={{
          width: "100%",
          height: "100%",
          display: "flex",
          flexDirection: "column",
          justifyContent: "center",
          padding: "80px",
          background: "#1a1a1a",
          position: "relative",
          fontFamily: "sans-serif",
        }}
      >
        <div
          style={{
            position: "absolute",
            top: -140,
            right: -160,
            width: 560,
            height: 560,
            borderRadius: "50%",
            background: "radial-gradient(circle at 35% 35%, #3b82f6, #2563eb 60%, transparent 75%)",
            opacity: 0.5,
            display: "flex",
          }}
        />
        <div
          style={{
            display: "flex",
            alignItems: "center",
            padding: "8px 20px",
            borderRadius: 999,
            background: "rgba(255,255,255,0.12)",
            color: "#3b82f6",
            fontSize: 24,
            fontWeight: 600,
            alignSelf: "flex-start",
            marginBottom: 32,
          }}
        >
          {SITE.tagline}
        </div>
        <div style={{ display: "flex", fontSize: 72, fontWeight: 700, color: "#ffffff", lineHeight: 1.1, maxWidth: 980 }}>
          Turn search traffic into your best sales channel.
        </div>
        <div style={{ display: "flex", fontSize: 32, color: "#e5e7eb", marginTop: 28 }}>{SITE.name}</div>
      </div>
    ),
    size,
  );
}
