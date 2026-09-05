import PricingTable from "@/components/pricing/PricingTable";

export const metadata = {
  title: "Pricing",
  description: "Simple, transparent pricing for Pro Builder CRM — plans for solo builders, teams, and multi-branch companies.",
  alternates: { canonical: "/pricing" },
};

export default function PricingPage() {
  return (
    <>
      <section style={{ background: "var(--color-bg-inverse)", padding: "var(--space-3xl) 0", textAlign: "center" }}>
        <div className="container" style={{ maxWidth: 720 }}>
          <p style={{ font: "var(--font-eyebrow)", color: "#a5b4fc", textTransform: "uppercase", letterSpacing: "0.08em", margin: "0 0 12px" }}>
            Pricing
          </p>
          <h1 style={{ font: "var(--font-h1)", color: "#fff", margin: "0 0 var(--space-sm)" }}>Simple pricing, no surprises</h1>
          <p style={{ font: "var(--font-body-lg)", color: "var(--gray-300)", margin: 0 }}>
            Pick the plan that matches how your business runs today — upgrade any time as your team grows.
          </p>
        </div>
      </section>

      <section className="section">
        <div className="container">
          <PricingTable />
        </div>
      </section>
    </>
  );
}
