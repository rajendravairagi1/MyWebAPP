"use client";

import { useState } from "react";

export default function Tooltip({ children, label }) {
  const [show, setShow] = useState(false);
  return (
    <span
      style={{ position: "relative", display: "inline-flex" }}
      onMouseEnter={() => setShow(true)}
      onMouseLeave={() => setShow(false)}
    >
      {children}
      {show && (
        <span
          style={{
            position: "absolute",
            bottom: "calc(100% + 8px)",
            left: "50%",
            transform: "translateX(-50%)",
            background: "var(--gray-900)",
            color: "var(--white)",
            padding: "6px 10px",
            borderRadius: "var(--radius-sm)",
            font: "var(--weight-medium) var(--text-xs)/1.2 var(--font-sans)",
            whiteSpace: "nowrap",
            boxShadow: "var(--shadow-md)",
            zIndex: 10,
          }}
        >
          {label}
        </span>
      )}
    </span>
  );
}
