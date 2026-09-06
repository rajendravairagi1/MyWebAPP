"use client";

import { useEffect, useState } from "react";

const SLIDES = [
  { src: "/screenshots/dashboard.png", alt: "Pro Builder CRM dashboard showing project revenue, collections and profit charts in the Nova dark theme" },
  { src: "/screenshots/analytics.png", alt: "Pro Builder CRM analytics view with unit booking status and payment collection rate charts" },
  { src: "/screenshots/brokers.png", alt: "Pro Builder CRM brokers module showing commission tracking and statements" },
];

export default function HeroCarousel() {
  const [index, setIndex] = useState(0);

  useEffect(() => {
    const timer = setInterval(() => {
      setIndex((current) => (current + 1) % SLIDES.length);
    }, 3800);
    return () => clearInterval(timer);
  }, []);

  return (
    <div
      style={{
        position: "relative",
        maxWidth: 960,
        margin: "0 auto",
        borderRadius: "var(--radius-lg)",
        overflow: "hidden",
        boxShadow: "0 30px 80px rgba(0,0,0,0.45)",
        border: "1px solid rgba(255,255,255,0.12)",
        background: "#0b1220",
      }}
    >
      <div
        style={{
          display: "flex",
          alignItems: "center",
          gap: 8,
          padding: "12px 16px",
          background: "#151d2e",
          borderBottom: "1px solid rgba(255,255,255,0.08)",
        }}
      >
        <span style={{ width: 11, height: 11, borderRadius: "50%", background: "#f87171" }} />
        <span style={{ width: 11, height: 11, borderRadius: "50%", background: "#fbbf24" }} />
        <span style={{ width: 11, height: 11, borderRadius: "50%", background: "#34d399" }} />
        <span style={{ marginLeft: 12, color: "#64748b", fontSize: "0.78rem" }}>app.probuildercrm.com</span>
      </div>

      <div style={{ position: "relative", aspectRatio: "16 / 10", background: "#0b1220" }}>
        {SLIDES.map((slide, slideIndex) => (
          // eslint-disable-next-line @next/next/no-img-element
          <img
            key={slide.src}
            src={slide.src}
            alt={slide.alt}
            style={{
              position: "absolute",
              inset: 0,
              width: "100%",
              height: "100%",
              objectFit: "cover",
              objectPosition: "top",
              opacity: slideIndex === index ? 1 : 0,
              transform: slideIndex === index ? "translateY(0)" : "translateY(14px)",
              transition: "opacity 0.9s ease, transform 0.9s ease",
            }}
          />
        ))}
      </div>

      <div style={{ display: "flex", justifyContent: "center", gap: 8, padding: "14px 0", background: "#151d2e" }}>
        {SLIDES.map((slide, slideIndex) => (
          <button
            key={slide.src}
            type="button"
            aria-label={`Show slide ${slideIndex + 1}`}
            onClick={() => setIndex(slideIndex)}
            style={{
              width: slideIndex === index ? 22 : 8,
              height: 8,
              borderRadius: 999,
              border: "none",
              background: slideIndex === index ? "var(--color-primary)" : "rgba(255,255,255,0.25)",
              cursor: "pointer",
              transition: "width 0.3s ease, background-color 0.3s ease",
              padding: 0,
            }}
          />
        ))}
      </div>
    </div>
  );
}
