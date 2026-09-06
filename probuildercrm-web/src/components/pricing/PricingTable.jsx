"use client";

import { useState } from "react";
import Link from "next/link";

const CYCLES = [
  { key: "monthly", label: "Monthly", months: 1, bonusMonths: 0 },
  { key: "half_yearly", label: "6 Months", months: 6, bonusMonths: 1 },
  { key: "yearly", label: "Yearly", months: 12, bonusMonths: 2 },
];

function cyclePricing(monthlyPrice, cycle) {
  const price = monthlyPrice * cycle.months;
  const originalPrice = Math.round(price / 0.6);
  return { price, originalPrice };
}

export default function PricingTable({ plans }) {
  const [cycleKey, setCycleKey] = useState("monthly");
  const cycle = CYCLES.find((c) => c.key === cycleKey);

  return (
    <div>
      <div style={{ display: "flex", justifyContent: "center", gap: 8, marginBottom: "var(--space-xl)", flexWrap: "wrap" }}>
        {CYCLES.map((c) => (
          <button
            key={c.key}
            type="button"
            onClick={() => setCycleKey(c.key)}
            style={{
              padding: "10px 20px",
              borderRadius: 999,
              border: cycleKey === c.key ? "2px solid var(--color-primary)" : "1px solid var(--color-border)",
              background: cycleKey === c.key ? "var(--color-primary-light)" : "#fff",
              color: cycleKey === c.key ? "var(--color-primary)" : "var(--color-ink)",
              fontWeight: 600,
              fontSize: "0.9rem",
              cursor: "pointer",
            }}
          >
            {c.label}
            {c.bonusMonths > 0 && (
              <span style={{ display: "block", fontSize: "0.72rem", fontWeight: 700, color: "var(--color-success)" }}>
                +{c.bonusMonths} month{c.bonusMonths > 1 ? "s" : ""} free
              </span>
            )}
          </button>
        ))}
      </div>

      <div className="grid-3">
        {plans.map((plan) => {
          const { price, originalPrice } = cyclePricing(plan.monthlyPrice, cycle);
          const totalMonths = cycle.months + cycle.bonusMonths;

          return (
            <div
              key={plan.slug}
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

              <span
                style={{
                  position: "absolute",
                  top: 16,
                  right: 16,
                  background: "#fee2e2",
                  color: "#b91c1c",
                  fontSize: "0.72rem",
                  fontWeight: 800,
                  padding: "3px 10px",
                  borderRadius: 999,
                }}
              >
                40% OFF
              </span>

              <div>
                <h3 style={{ font: "var(--font-h3)", fontSize: "1.3rem", margin: "0 0 4px" }}>{plan.name}</h3>
                <p style={{ margin: 0, color: "var(--color-ink-soft)", fontSize: "0.9rem" }}>{plan.description}</p>
              </div>

              <div>
                <div style={{ display: "flex", alignItems: "baseline", gap: 8 }}>
                  <span style={{ fontSize: "1.1rem", color: "var(--color-ink-soft)", textDecoration: "line-through" }}>
                    ₹{originalPrice.toLocaleString("en-IN")}
                  </span>
                  <span style={{ fontSize: "2rem", fontWeight: 800 }}>₹{price.toLocaleString("en-IN")}</span>
                </div>
                <div style={{ color: "var(--color-ink-soft)", fontSize: "0.85rem" }}>
                  {cycle.bonusMonths > 0
                    ? `for ${cycle.months} months — ${totalMonths} months of service`
                    : "per month"}
                </div>
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
                Book a Demo
              </Link>
            </div>
          );
        })}
      </div>
    </div>
  );
}
