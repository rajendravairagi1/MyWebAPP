"use client";

import { useEffect, useState } from "react";

function cyclePreview(monthlyPrice, months, bonusMonths) {
  const price = monthlyPrice * months;
  const originalPrice = Math.round(price / 0.6);
  return { price, originalPrice, totalMonths: months + bonusMonths };
}

export default function PricingAdmin() {
  const [plans, setPlans] = useState([]);
  const [loading, setLoading] = useState(true);
  const [editingSlug, setEditingSlug] = useState(null);
  const [form, setForm] = useState(null);
  const [saving, setSaving] = useState(false);
  const [error, setError] = useState("");

  async function loadPlans() {
    setLoading(true);
    const response = await fetch("/api/admin/pricing");
    const body = await response.json();
    setPlans(body.plans || []);
    setLoading(false);
  }

  useEffect(() => {
    loadPlans();
  }, []);

  function startEdit(plan) {
    setForm({
      name: plan.name,
      description: plan.description,
      monthlyPrice: plan.monthlyPrice,
      features: plan.features,
    });
    setEditingSlug(plan.slug);
    setError("");
  }

  async function handleSave(event) {
    event.preventDefault();
    setSaving(true);
    setError("");

    try {
      const response = await fetch(`/api/admin/pricing/${editingSlug}`, {
        method: "PUT",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(form),
      });
      const body = await response.json();
      if (!response.ok) throw new Error(body.error || "Failed to save plan.");

      await loadPlans();
      setEditingSlug(null);
    } catch (err) {
      setError(err.message);
    } finally {
      setSaving(false);
    }
  }

  if (loading) return <p>Loading…</p>;

  return (
    <div style={{ display: "flex", flexDirection: "column", gap: 16 }}>
      <p style={{ color: "var(--color-ink-soft)", fontSize: "0.9rem", margin: 0 }}>
        Set each plan&apos;s real monthly price here — the pricing page works it out from there: the 6-month price is
        6× that (with 1 month free added to the service), the yearly price is 12× that (with 2 months free added).
        The permanent &quot;40% OFF&quot; badge and struck-through price shown to visitors are calculated
        automatically too — you only ever edit the one real monthly number per plan.
      </p>

      {plans.map((plan) => (
        <div key={plan.slug} className="card">
          {editingSlug === plan.slug ? (
            <form onSubmit={handleSave} style={{ display: "flex", flexDirection: "column", gap: 12 }}>
              <div style={{ display: "grid", gridTemplateColumns: "1fr 1fr", gap: 12 }}>
                <div>
                  <label style={labelStyle}>Plan name</label>
                  <input value={form.name} onChange={(e) => setForm({ ...form, name: e.target.value })} style={inputStyle} required />
                </div>
                <div>
                  <label style={labelStyle}>Monthly price (₹)</label>
                  <input
                    type="number"
                    min="0"
                    value={form.monthlyPrice}
                    onChange={(e) => setForm({ ...form, monthlyPrice: Number(e.target.value) })}
                    style={inputStyle}
                    required
                  />
                </div>
              </div>
              <div>
                <label style={labelStyle}>Description</label>
                <input value={form.description} onChange={(e) => setForm({ ...form, description: e.target.value })} style={inputStyle} required />
              </div>
              <div>
                <label style={labelStyle}>Features (one per line)</label>
                <textarea
                  rows={6}
                  value={form.features.join("\n")}
                  onChange={(e) => setForm({ ...form, features: e.target.value.split("\n") })}
                  style={{ ...inputStyle, resize: "vertical" }}
                />
              </div>

              <div style={{ background: "var(--color-bg-soft)", borderRadius: 8, padding: 12, fontSize: "0.85rem" }}>
                <strong>Live preview at ₹{form.monthlyPrice}/month:</strong>
                <ul style={{ margin: "6px 0 0", paddingLeft: 18 }}>
                  {["monthly", "half_yearly", "yearly"].map((cycle) => {
                    const months = { monthly: 1, half_yearly: 6, yearly: 12 }[cycle];
                    const bonus = { monthly: 0, half_yearly: 1, yearly: 2 }[cycle];
                    const { price, originalPrice, totalMonths } = cyclePreview(form.monthlyPrice, months, bonus);
                    return (
                      <li key={cycle}>
                        {cycle}: <s>₹{originalPrice}</s> <strong>₹{price}</strong>
                        {bonus > 0 ? ` (${months} months paid, ${totalMonths} months of service)` : ""}
                      </li>
                    );
                  })}
                </ul>
              </div>

              {error && <p style={{ color: "#dc2626", margin: 0 }}>{error}</p>}

              <div style={{ display: "flex", gap: 8 }}>
                <button type="submit" className="btn btn-primary" disabled={saving}>
                  {saving ? "Saving…" : "Save"}
                </button>
                <button type="button" className="btn btn-secondary" onClick={() => setEditingSlug(null)}>
                  Cancel
                </button>
              </div>
            </form>
          ) : (
            <div style={{ display: "flex", justifyContent: "space-between", alignItems: "center" }}>
              <div>
                <h3 style={{ font: "var(--font-h3)", margin: "0 0 4px" }}>{plan.name}</h3>
                <p style={{ margin: 0, color: "var(--color-ink-soft)", fontSize: "0.9rem" }}>
                  ₹{plan.monthlyPrice}/month · {plan.features.length} features
                </p>
              </div>
              <button type="button" className="btn btn-secondary" onClick={() => startEdit(plan)}>
                Edit
              </button>
            </div>
          )}
        </div>
      ))}
    </div>
  );
}

const labelStyle = { display: "block", fontWeight: 600, marginBottom: 4, fontSize: "0.85rem" };
const inputStyle = { width: "100%", padding: "8px 10px", borderRadius: 6, border: "1px solid var(--color-border)", font: "var(--font-body)" };
