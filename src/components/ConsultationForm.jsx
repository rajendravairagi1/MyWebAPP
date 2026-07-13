"use client";

import { useState } from "react";
import Input from "./ui/Input";
import Textarea from "./ui/Textarea";
import Button from "./ui/Button";
import Toast from "./ui/Toast";

export default function ConsultationForm({ onSuccess, source = "website" }) {
  const [form, setForm] = useState({ name: "", email: "", phone: "", message: "" });
  const [status, setStatus] = useState("idle"); // idle | submitting | success | error
  const [error, setError] = useState("");

  const update = (key) => (e) => setForm((f) => ({ ...f, [key]: e.target.value }));

  async function handleSubmit(e) {
    e.preventDefault();
    setStatus("submitting");
    setError("");
    try {
      const res = await fetch("/api/contact", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ ...form, source }),
      });
      const data = await res.json();
      if (!res.ok) throw new Error(data.error || "Something went wrong. Please try again.");
      setStatus("success");
      onSuccess && onSuccess();
    } catch (err) {
      setStatus("error");
      setError(err.message);
    }
  }

  if (status === "success") {
    return <Toast message="Thanks! We'll be in touch within one business day." tone="success" />;
  }

  return (
    <form onSubmit={handleSubmit} style={{ display: "flex", flexDirection: "column", gap: "14px" }}>
      <Input label="Name" required placeholder="Your name" value={form.name} onChange={update("name")} />
      <Input label="Work Email" type="email" required placeholder="you@company.com" value={form.email} onChange={update("email")} />
      <Input label="Phone / WhatsApp" type="tel" placeholder="+1 555 000 0000" value={form.phone} onChange={update("phone")} />
      <Textarea
        label="What are you looking to grow?"
        required
        placeholder="Tell us about your goals"
        rows={4}
        value={form.message}
        onChange={update("message")}
      />
      {status === "error" && <Toast message={error} tone="danger" onClose={() => setStatus("idle")} />}
      <Button variant="primary" type="submit" disabled={status === "submitting"}>
        {status === "submitting" ? "Sending…" : "Request Consultation"}
      </Button>
    </form>
  );
}
