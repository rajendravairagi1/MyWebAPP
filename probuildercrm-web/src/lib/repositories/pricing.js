import { getDb } from "@/lib/db";

const DEFAULT_PLANS = [
  {
    slug: "solo",
    name: "Solo",
    description: "For one builder managing their own projects, start to finish.",
    monthly_price: 999,
    highlighted: 0,
    sort_order: 1,
    features: [
      "Unlimited projects & units",
      "Customer bookings & payments",
      "Quotations & invoices",
      "Property brochure sharing",
      "Ledger & reports",
    ],
  },
  {
    slug: "team",
    name: "Builder Team",
    description: "For a builder with supervisors, sales staff or site managers.",
    monthly_price: 2499,
    highlighted: 1,
    sort_order: 2,
    features: [
      "Everything in Solo",
      "Team members with role-based access",
      "Contractor & vendor ledgers",
      "Broker commission tracking",
      "Investor accounts",
      "Loan disbursement tracking",
    ],
  },
  {
    slug: "company",
    name: "Company",
    description: "For a company running multiple branches or cities.",
    monthly_price: 4999,
    highlighted: 0,
    sort_order: 3,
    features: [
      "Everything in Builder Team",
      "Multiple branches under one account",
      "Combined company-wide dashboard",
      "Priority support",
    ],
  },
];

function ensureTable() {
  const db = getDb();
  db.exec(`
    CREATE TABLE IF NOT EXISTS pricing_plans (
      id INTEGER PRIMARY KEY AUTOINCREMENT,
      slug TEXT UNIQUE NOT NULL,
      name TEXT NOT NULL,
      description TEXT NOT NULL,
      monthly_price INTEGER NOT NULL,
      highlighted INTEGER NOT NULL DEFAULT 0,
      sort_order INTEGER NOT NULL DEFAULT 0,
      features TEXT NOT NULL,
      updated_at TEXT NOT NULL DEFAULT (datetime('now'))
    );
  `);

  const { count } = db.prepare("SELECT COUNT(*) as count FROM pricing_plans").get();
  if (count === 0) {
    const insert = db.prepare(
      `INSERT INTO pricing_plans (slug, name, description, monthly_price, highlighted, sort_order, features)
       VALUES (?, ?, ?, ?, ?, ?, ?)`,
    );
    for (const plan of DEFAULT_PLANS) {
      insert.run(plan.slug, plan.name, plan.description, plan.monthly_price, plan.highlighted, plan.sort_order, JSON.stringify(plan.features));
    }
  }
}

function rowToPlan(row) {
  if (!row) return null;
  return {
    slug: row.slug,
    name: row.name,
    description: row.description,
    monthlyPrice: row.monthly_price,
    highlighted: !!row.highlighted,
    sortOrder: row.sort_order,
    features: JSON.parse(row.features),
    updatedAt: row.updated_at,
  };
}

export function listPlans() {
  ensureTable();
  const rows = getDb().prepare("SELECT * FROM pricing_plans ORDER BY sort_order ASC").all();
  return rows.map(rowToPlan);
}

export function getPlanBySlug(slug) {
  ensureTable();
  const row = getDb().prepare("SELECT * FROM pricing_plans WHERE slug = ?").get(slug);
  return rowToPlan(row);
}

export function updatePlan(slug, { name, description, monthlyPrice, features }) {
  ensureTable();
  getDb()
    .prepare(
      `UPDATE pricing_plans
       SET name = ?, description = ?, monthly_price = ?, features = ?, updated_at = datetime('now')
       WHERE slug = ?`,
    )
    .run(name, description, monthlyPrice, JSON.stringify(features), slug);

  return getPlanBySlug(slug);
}

/**
 * Turns one plan's admin-set monthly_price into everything the pricing
 * page displays: the real price for each billing cycle (monthly/6-month/
 * yearly, each just monthly_price times the cycle's months — no separate
 * number to keep in sync), how many bonus months a longer cycle includes,
 * and a permanent "40% off" anchor — a struck-through original price
 * computed as the real price divided by 0.6, purely a display device
 * (the actual amount charged is always the plan's own monthly_price
 * times the cycle length, exactly as configured in /admin).
 */
export function pricingForCycle(plan, cycle) {
  const months = { monthly: 1, half_yearly: 6, yearly: 12 }[cycle];
  const bonusMonths = { monthly: 0, half_yearly: 1, yearly: 2 }[cycle];

  const price = plan.monthlyPrice * months;
  const originalPrice = Math.round(price / 0.6);

  return {
    months,
    bonusMonths,
    price,
    originalPrice,
    perMonthEquivalent: Math.round(price / (months + bonusMonths)),
  };
}
