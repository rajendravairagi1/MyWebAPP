"use client";

import { useState } from "react";
import Link from "next/link";
import { PRICING_PLANS } from "@/data/pricing";

export default function PricingTable() {
  const [yearly, setYearly] = useState(false);

  return (
    <div>
      <div style={{ display: "flex", justifyContent: "center", alignItems: "center", gap: 12, marginBottom: "var(--space-xl)" }}>
        <span style={{ fontWeight: yearly ? 400 : 700 }}>Monthly</span>
        <button
          type="button"
          onClick={() => setYearly((v) => !v)}
          aria-label="Toggle yearly pricing"
          style={{
            width: 48,
            height: 26,
            borderRadius: 999,
            background: yearly ? "var(--color-primary)" : "#cbd5e1",
            border: "none",
            position: "relative",
            cursor: "pointer",
            transition: "background 0.15s ease",
          }}
        >
          <span
            style={{
              position: "absolute",
              top: 3,
              left: yearly ? 25 : 3,
              width: 20,
              height: 20,
              borderRadius: "50%",
              background: "#fff",
              transition: "left 0.15s ease",
            }}
          />
        </button>
        <span style={{ fontWeight: yearly ? 700 : 400 }}>
          Yearly <span style={{ color: "var(--color-success)", fontSize: "0.8rem" }}>(save ~17%)</span>
        </span>
      </div>

      <div className="grid-3">
        {PRICING_PLANS.map((plan) => (
          <div
            key={plan.name}
            className="card"
            style={{
              display: "flex",
              flexDirection: "column",
              gap: 16,
              border: plan.highlighted ? "2px solid var(--color-primary)" : undefined,
              position: "relative",
            }}
          >
            {plan.highlighted && (
              <span
                style={{
                  position: "absolute",
                  top: -14,
                  left: "50%",
                  transform: "translateX(-50%)",
                  background: "var(--color-primary)",
                  color: "#fff",
                  fontSize: "0.75rem",
                  fontWeight: 700,
                  padding: "4px 12px",
                  borderRadius: 999,
                }}
              >
                Most Popular
              </span>
            )}
            <div>
              <h3 style={{ font: "var(--font-h3)", fontSize: "1.3rem", margin: "0 0 4px" }}>{plan.name}</h3>
              <p style={{ margin: 0, color: "var(--color-ink-soft)", fontSize: "0.9rem" }}>{plan.description}</p>
            </div>
            <div>
              <span style={{ fontSize: "2rem", fontWeight: 800 }}>
                {plan.currency}
                {(yearly ? plan.priceYearly : plan.priceMonthly).toLocaleString("en-IN")}
              </span>
              <span style={{ color: "var(--color-ink-soft)" }}>/month</span>
            </div>
            <ul style={{ listStyle: "none", padding: 0, margin: 0, display: "flex", flexDirection: "column", gap: 10 }}>
              {plan.features.map((feature) => (
                <li key={feature} style={{ display: "flex", gap: 8, alignItems: "flex-start", fontSize: "0.92rem" }}>
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--color-success)" strokeWidth="2.5" style={{ flexShrink: 0, marginTop: 2 }}>
                    <path strokeLinecap="round" strokeLinejoin="round" d="M20 6L9 17l-5-5" />
                  </svg>
                  {feature}
                </li>
              ))}
            </ul>
            <Link
              href="/contact"
              className={plan.highlighted ? "btn btn-primary" : "btn btn-secondary"}
              style={{ marginTop: "auto", justifyContent: "center" }}
            >
              Book a Free Demo
            </Link>
          </div>
        ))}
      </div>
    </div>
  );
}
