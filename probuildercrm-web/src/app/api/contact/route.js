import { NextResponse } from "next/server";
import { getDb } from "@/lib/db";

function ensureTable() {
  getDb().exec(`
    CREATE TABLE IF NOT EXISTS contact_submissions (
      id INTEGER PRIMARY KEY AUTOINCREMENT,
      name TEXT NOT NULL,
      email TEXT NOT NULL,
      phone TEXT,
      message TEXT NOT NULL,
      created_at TEXT NOT NULL DEFAULT (datetime('now'))
    );
  `);
}

export async function POST(request) {
  const body = await request.json().catch(() => null);
  if (!body || !body.name || !body.email || !body.message) {
    return NextResponse.json({ error: "Name, email and message are required." }, { status: 400 });
  }

  ensureTable();
  getDb()
    .prepare("INSERT INTO contact_submissions (name, email, phone, message) VALUES (?, ?, ?, ?)")
    .run(body.name, body.email, body.phone || null, body.message);

  return NextResponse.json({ ok: true });
}
