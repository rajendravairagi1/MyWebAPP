"use client";

import { useState } from "react";

export default function ContactForm() {
  const [form, setForm] = useState({ name: "", email: "", phone: "", message: "" });
  const [status, setStatus] = useState("idle"); // idle | sending | sent | error

  async function handleSubmit(event) {
    event.preventDefault();
    setStatus("sending");

    try {
      const response = await fetch("/api/contact", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(form),
      });

      if (!response.ok) throw new Error();
      setStatus("sent");
      setForm({ name: "", email: "", phone: "", message: "" });
    } catch {
      setStatus("error");
    }
  }

  if (status === "sent") {
    return (
      <div className="card" style={{ textAlign: "center" }}>
        <h3 style={{ font: "var(--font-h3)" }}>Thanks — we&apos;ll be in touch shortly.</h3>
        <p style={{ color: "var(--color-ink-soft)" }}>We usually reply within one business day.</p>
      </div>
    );
  }

  return (
    <form onSubmit={handleSubmit} className="card" style={{ display: "flex", flexDirection: "column", gap: 16 }}>
      <div>
        <label htmlFor="name" style={{ display: "block", fontWeight: 600, marginBottom: 6, fontSize: "0.9rem" }}>
          Name
        </label>
        <input
          id="name"
          required
          value={form.name}
          onChange={(e) => setForm({ ...form, name: e.target.value })}
          style={inputStyle}
        />
      </div>
      <div>
        <label htmlFor="email" style={{ display: "block", fontWeight: 600, marginBottom: 6, fontSize: "0.9rem" }}>
          Email
        </label>
        <input
          id="email"
          type="email"
          required
          value={form.email}
          onChange={(e) => setForm({ ...form, email: e.target.value })}
          style={inputStyle}
        />
      </div>
      <div>
        <label htmlFor="phone" style={{ display: "block", fontWeight: 600, marginBottom: 6, fontSize: "0.9rem" }}>
          Phone (optional)
        </label>
        <input id="phone" value={form.phone} onChange={(e) => setForm({ ...form, phone: e.target.value })} style={inputStyle} />
      </div>
      <div>
        <label htmlFor="message" style={{ display: "block", fontWeight: 600, marginBottom: 6, fontSize: "0.9rem" }}>
          Tell us about your business
        </label>
        <textarea
          id="message"
          required
          rows={4}
          value={form.message}
          onChange={(e) => setForm({ ...form, message: e.target.value })}
          style={{ ...inputStyle, resize: "vertical" }}
        />
      </div>

      {status === "error" && (
        <p style={{ color: "#dc2626", fontSize: "0.9rem", margin: 0 }}>Something went wrong — please try again.</p>
      )}

      <button type="submit" className="btn btn-primary btn-lg" disabled={status === "sending"} style={{ justifyContent: "center" }}>
        {status === "sending" ? "Sending…" : "Book a Free Demo"}
      </button>
    </form>
  );
}

const inputStyle = {
  width: "100%",
  padding: "10px 14px",
  borderRadius: "var(--radius-sm)",
  border: "1px solid var(--color-border)",
  font: "var(--font-body)",
};
