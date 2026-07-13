"use client";

import { useEffect, useRef, useState } from "react";

export default function StatCounter({ value, suffix = "", label }) {
  const [display, setDisplay] = useState(0);
  const ref = useRef(null);

  useEffect(() => {
    const el = ref.current;
    if (!el) return;
    const io = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            const duration = 1500;
            const start = performance.now();
            const tick = (now) => {
              const p = Math.min((now - start) / duration, 1);
              setDisplay(Math.round(value * (1 - Math.pow(1 - p, 3))));
              if (p < 1) requestAnimationFrame(tick);
            };
            requestAnimationFrame(tick);
            io.disconnect();
          }
        });
      },
      { threshold: 0.4 },
    );
    io.observe(el);
    return () => io.disconnect();
  }, [value]);

  return (
    <div ref={ref} style={{ display: "flex", flexDirection: "column", gap: "6px", textAlign: "center", fontFamily: "var(--font-sans)" }}>
      <span
        style={{
          font: "var(--weight-bold) var(--text-4xl)/1 var(--font-sans)",
          color: "var(--color-accent-dark)",
          letterSpacing: "var(--tracking-tight)",
        }}
      >
        {display}
        {suffix}
      </span>
      <span style={{ font: "var(--font-body-sm)", color: "var(--color-text-secondary)" }}>{label}</span>
    </div>
  );
}
