import { NextResponse } from "next/server";
import { SITE } from "@/data/site";

const EMAIL_RE = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

function escapeHtml(str) {
  return str.replace(/[&<>"']/g, (c) => ({ "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#39;" })[c]);
}

async function sendLeadEmail({ name, email, phone, message, source }) {
  const apiKey = process.env.RESEND_API_KEY;
  if (!apiKey) return { sent: false, reason: "no-api-key" };

  const to = process.env.RESEND_TO_EMAIL || SITE.email;
  const from = process.env.RESEND_FROM_EMAIL || "Oneweblink Website <onboarding@resend.dev>";

  const res = await fetch("https://api.resend.com/emails", {
    method: "POST",
    headers: {
      Authorization: `Bearer ${apiKey}`,
      "Content-Type": "application/json",
    },
    body: JSON.stringify({
      from,
      to,
      reply_to: email,
      subject: `New consultation request from ${name} (${source})`,
      html: `
        <p><strong>Name:</strong> ${escapeHtml(name)}</p>
        <p><strong>Email:</strong> ${escapeHtml(email)}</p>
        <p><strong>Phone:</strong> ${escapeHtml(phone || "—")}</p>
        <p><strong>Source:</strong> ${escapeHtml(source)}</p>
        <p><strong>Message:</strong></p>
        <p>${escapeHtml(message).replace(/\n/g, "<br>")}</p>
      `,
    }),
  });

  if (!res.ok) {
    const detail = await res.text().catch(() => "");
    throw new Error(`Resend API error (${res.status}): ${detail}`);
  }
  return { sent: true };
}

export async function POST(request) {
  let body;
  try {
    body = await request.json();
  } catch {
    return NextResponse.json({ error: "Invalid request body." }, { status: 400 });
  }

  const name = (body.name || "").trim();
  const email = (body.email || "").trim();
  const phone = (body.phone || "").trim();
  const message = (body.message || "").trim();
  const source = (body.source || "website").trim();

  if (!name || !email || !message) {
    return NextResponse.json({ error: "Name, email, and message are required." }, { status: 400 });
  }
  if (!EMAIL_RE.test(email)) {
    return NextResponse.json({ error: "Please enter a valid email address." }, { status: 400 });
  }

  const lead = { name, email, phone, message, source, at: new Date().toISOString() };

  let emailResult;
  try {
    emailResult = await sendLeadEmail(lead);
  } catch (err) {
    // Email delivery failing shouldn't lose the lead — it's still logged below.
    console.error("[contact] email delivery failed:", err.message);
    emailResult = { sent: false, reason: "delivery-error" };
  }

  // Always logged server-side so every submission is captured even without email configured.
  console.log("[contact] new lead:", lead, emailResult);

  return NextResponse.json({ ok: true });
}
