"use client";

import { useState } from "react";

const TABS = [
  {
    key: "dashboard",
    label: "Dashboard & Analytics",
    title: "See exactly where your business stands, today",
    description:
      "Revenue trends, invoice status, unit booking status, deal pipeline, top projects by profit and payment collection rate — all in one dashboard, updated the moment a payment is recorded.",
    image: "/screenshots/dashboard.png",
    alt: "Pro Builder CRM dashboard with revenue trend, invoice status and payment collection charts",
  },
  {
    key: "analytics",
    label: "Projects & Units",
    title: "Every project, every unit, always up to date",
    description:
      "Track unit status, pricing and booking pipeline across every project — with the numbers that matter surfaced automatically, not buried in a spreadsheet.",
    image: "/screenshots/analytics.png",
    alt: "Pro Builder CRM analytics view showing unit booking status and deal pipeline",
  },
  {
    key: "brokers",
    label: "Brokers & Team",
    title: "Commissions and access, handled properly",
    description:
      "Track every broker's commission earned, paid and owed, and give your team exactly the access they need — without exposing prices or profit if you'd rather they didn't see it.",
    image: "/screenshots/brokers.png",
    alt: "Pro Builder CRM brokers module with commission tracking",
  },
  {
    key: "settings",
    label: "Business Settings",
    title: "Set it up the way your business actually runs",
    description:
      "Currency, branding, multi-branch/company rollups, language — configure Pro Builder CRM around your business, not the other way around.",
    image: "/screenshots/settings.png",
    alt: "Pro Builder CRM business settings screen",
  },
];

export default function FeatureTabs() {
  const [activeKey, setActiveKey] = useState(TABS[0].key);
  const active = TABS.find((tab) => tab.key === activeKey);

  return (
    <div>
      <div
        style={{
          display: "flex",
          gap: 8,
          flexWrap: "wrap",
          justifyContent: "center",
          marginBottom: "var(--space-xl)",
        }}
      >
        {TABS.map((tab) => (
          <button
            key={tab.key}
            type="button"
            onClick={() => setActiveKey(tab.key)}
            style={{
              padding: "10px 18px",
              borderRadius: 999,
              border: activeKey === tab.key ? "2px solid var(--color-primary)" : "1px solid var(--color-border)",
              background: activeKey === tab.key ? "var(--color-primary-light)" : "#fff",
              color: activeKey === tab.key ? "var(--color-primary)" : "var(--color-ink)",
              fontWeight: 600,
              fontSize: "0.9rem",
              cursor: "pointer",
              transition: "all 0.2s ease",
            }}
          >
            {tab.label}
          </button>
        ))}
      </div>

      <div
        style={{
          display: "grid",
          gridTemplateColumns: "1fr 1.3fr",
          gap: "var(--space-xl)",
          alignItems: "center",
        }}
        className="feature-tabs-grid"
      >
        <div>
          <h3 style={{ font: "var(--font-h3)", fontSize: "1.5rem", margin: "0 0 12px" }}>{active.title}</h3>
          <p style={{ color: "var(--color-ink-soft)", fontSize: "1.02rem", margin: 0 }}>{active.description}</p>
        </div>

        <div
          style={{
            borderRadius: "var(--radius-lg)",
            overflow: "hidden",
            border: "1px solid var(--color-border)",
            boxShadow: "var(--shadow-lg)",
          }}
        >
          {/* eslint-disable-next-line @next/next/no-img-element */}
          <img
            key={active.image}
            src={active.image}
            alt={active.alt}
            style={{ width: "100%", display: "block" }}
          />
        </div>
      </div>

      <style>{`
        @media (max-width: 860px) {
          .feature-tabs-grid {
            grid-template-columns: 1fr !important;
          }
        }
      `}</style>
    </div>
  );
}
