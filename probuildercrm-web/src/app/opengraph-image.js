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
          padding: 80,
          background: "#0f172a",
          color: "#fff",
          fontFamily: "sans-serif",
        }}
      >
        <div style={{ display: "flex", alignItems: "center", gap: 16, marginBottom: 32 }}>
          <div
            style={{
              width: 56,
              height: 56,
              borderRadius: 14,
              background: "#4f46e5",
              display: "flex",
              alignItems: "center",
              justifyContent: "center",
              fontSize: 32,
              fontWeight: 800,
            }}
          >
            P
          </div>
          <div style={{ fontSize: 32, fontWeight: 700 }}>Pro Builder CRM</div>
        </div>
        <div style={{ fontSize: 52, fontWeight: 800, maxWidth: 980, lineHeight: 1.15 }}>{SITE.tagline}</div>
      </div>
    ),
    size,
  );
}
