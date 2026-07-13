"use client";

import { useState } from "react";
import Link from "next/link";
import Icon from "@/components/ui/Icon";
import Badge from "@/components/ui/Badge";
import Button from "@/components/ui/Button";
import ServiceCard from "@/components/ui/ServiceCard";
import StatCounter from "@/components/ui/StatCounter";
import Dialog from "@/components/ui/Dialog";
import ConsultationForm from "@/components/ConsultationForm";
import { SITE, COUNTRIES, CERTIFICATIONS, TRACK_RECORD } from "@/data/site";
import { SERVICES } from "@/data/services";
import { INDUSTRIES } from "@/data/industries";
import { PROCESS_STEPS, WHY_BLOCKS } from "@/data/process";
import { RESULTS, TESTIMONIALS, FAQS } from "@/data/testimonials";

function TrackRecordCard({ kind, title, subtitle, stat, href, cta }) {
  return (
    <a
      href={href}
      target="_blank"
      rel="noopener noreferrer"
      style={{
        display: "flex",
        flexDirection: "column",
        gap: "var(--space-sm)",
        padding: "var(--space-lg)",
        border: "1px solid var(--color-border)",
        borderRadius: "var(--radius-lg)",
        background: "var(--color-bg)",
        textDecoration: "none",
        boxShadow: "var(--shadow-sm)",
      }}
    >
      <div
        style={{
          width: "40px",
          height: "40px",
          borderRadius: "50%",
          background: "var(--color-accent-tint-weak)",
          display: "flex",
          alignItems: "center",
          justifyContent: "center",
          font: "var(--weight-semibold) var(--text-sm)/1 var(--font-sans)",
          color: "var(--color-accent-dark)",
        }}
      >
        {kind === "in" ? "in" : <Icon name="star" size={18} />}
      </div>
      <div>
        <h3 style={{ font: "var(--font-h4)", color: "var(--color-text-primary)", margin: "0 0 2px" }}>{title}</h3>
        <p style={{ font: "var(--font-body-sm)", color: "var(--color-text-secondary)", margin: 0 }}>{subtitle}</p>
      </div>
      <p style={{ font: "var(--font-body-sm)", color: "var(--color-text-primary)", margin: 0 }}>{stat}</p>
      <span style={{ font: "var(--font-label)", color: "var(--color-accent-dark)", display: "flex", alignItems: "center", gap: "6px" }}>
        {cta} <Icon name="arrow-up-right" size={14} />
      </span>
    </a>
  );
}

function IndustryCard({ slug, icon, title, description }) {
  return (
    <Link
      href={`/industries/${slug}`}
      style={{
        border: "1px solid var(--color-border)",
        borderRadius: "var(--radius-lg)",
        padding: "var(--space-lg)",
        display: "flex",
        flexDirection: "column",
        alignItems: "center",
        textAlign: "center",
        gap: "var(--space-sm)",
        background: "var(--color-bg)",
        textDecoration: "none",
      }}
    >
      <div
        style={{
          width: "48px",
          height: "48px",
          borderRadius: "50%",
          background: "var(--color-accent-tint-weak)",
          display: "flex",
          alignItems: "center",
          justifyContent: "center",
        }}
      >
        <Icon name={icon} size={22} style={{ color: "var(--color-accent-dark)" }} />
      </div>
      <h3 style={{ font: "var(--font-h4)", color: "var(--color-text-primary)", margin: 0 }}>{title}</h3>
      <p style={{ font: "var(--font-body-sm)", color: "var(--color-text-secondary)", margin: 0 }}>{description}</p>
    </Link>
  );
}

function ProcessStep({ number, title, description, subs, isLast }) {
  return (
    <div style={{ display: "grid", gridTemplateColumns: "56px 1fr", gap: "var(--space-lg)" }}>
      <div style={{ display: "flex", flexDirection: "column", alignItems: "center" }}>
        <div
          style={{
            width: "44px",
            height: "44px",
            borderRadius: "50%",
            border: "2px solid var(--blue-500)",
            display: "flex",
            alignItems: "center",
            justifyContent: "center",
            color: "var(--white)",
            font: "var(--weight-semibold) var(--text-base)/1 var(--font-sans)",
            flexShrink: 0,
          }}
        >
          {number}
        </div>
        {!isLast && <div style={{ width: "2px", flexGrow: 1, background: "rgba(255,255,255,0.15)", marginTop: "8px" }} />}
      </div>
      <div style={{ paddingBottom: "var(--space-2xl)" }}>
        <h3 style={{ font: "var(--font-h3)", color: "var(--white)", margin: "0 0 8px" }}>{title}</h3>
        <p style={{ font: "var(--font-body)", color: "var(--gray-300)", margin: "0 0 var(--space-md)", maxWidth: "560px" }}>{description}</p>
        <div style={{ display: "flex", gap: "10px", flexWrap: "wrap" }}>
          {subs.map((s) => (
            <span
              key={s}
              style={{
                font: "var(--font-body-sm)",
                color: "var(--blue-500)",
                border: "1px solid rgba(59,130,246,0.4)",
                borderRadius: "var(--radius-pill)",
                padding: "6px 14px",
              }}
            >
              {s}
            </span>
          ))}
        </div>
      </div>
    </div>
  );
}

function TestimonialRow({ number, quote, name, role, location, isLast }) {
  return (
    <div style={{ padding: "var(--space-lg) 0", borderBottom: isLast ? "none" : "1px solid var(--color-border)" }}>
      <span style={{ font: "var(--font-eyebrow)", color: "var(--color-text-secondary)" }}>/{number}</span>
      <p
        style={{
          font: "var(--font-h4)",
          color: "var(--color-text-primary)",
          margin: "8px 0 var(--space-sm)",
          maxWidth: "620px",
          fontWeight: "var(--weight-regular)",
        }}
      >
        &#8220;{quote}&#8221;
      </p>
      <p style={{ font: "var(--font-body-sm)", color: "var(--color-text-secondary)", margin: 0 }}>
        <strong style={{ color: "var(--color-text-primary)" }}>{name}</strong> &middot; {role} &middot; {location}
      </p>
    </div>
  );
}

function FaqItem({ q, a, open, onToggle }) {
  return (
    <div style={{ borderBottom: "1px solid var(--color-border)" }}>
      <button
        onClick={onToggle}
        style={{
          width: "100%",
          background: "none",
          border: "none",
          cursor: "pointer",
          display: "flex",
          alignItems: "flex-start",
          justifyContent: "space-between",
          gap: "var(--space-sm)",
          padding: "var(--space-md) 0",
          textAlign: "left",
          fontFamily: "var(--font-sans)",
        }}
      >
        <span style={{ font: "var(--font-h4)", color: "var(--color-text-primary)" }}>{q}</span>
        <Icon name={open ? "minus" : "plus"} size={20} style={{ color: "var(--color-accent-dark)", flexShrink: 0, marginTop: "4px" }} />
      </button>
      {open && <p style={{ font: "var(--font-body)", color: "var(--color-text-secondary)", margin: "0 0 var(--space-md)" }}>{a}</p>}
    </div>
  );
}

export default function Home() {
  const [dialogOpen, setDialogOpen] = useState(false);
  const [openFaq, setOpenFaq] = useState(0);

  return (
    <div style={{ fontFamily: "var(--font-sans)", background: "var(--color-bg)" }}>
      {/* HERO + INTRO + CERTIFICATIONS */}
      <section style={{ background: "var(--color-bg-inverse)", position: "relative", overflow: "hidden" }}>
        <div
          style={{
            position: "absolute",
            top: "-120px",
            right: "-160px",
            width: "560px",
            height: "560px",
            borderRadius: "50%",
            background: "radial-gradient(circle at 35% 35%, var(--blue-500), var(--blue-600) 60%, transparent 75%)",
            filter: "blur(2px)",
            opacity: 0.5,
          }}
        />

        <div
          className="stack-on-mobile"
          style={{
            maxWidth: "var(--container-max)",
            margin: "0 auto",
            padding: "var(--space-3xl) var(--container-padding) var(--space-2xl)",
            display: "grid",
            gridTemplateColumns: "1.05fr 0.95fr",
            gap: "var(--space-2xl)",
            alignItems: "center",
            position: "relative",
          }}
        >
          <div>
            <Badge tone="accent">{SITE.tagline}</Badge>
            <h1 style={{ font: "var(--font-display)", color: "var(--white)", letterSpacing: "var(--tracking-tight)", margin: "var(--space-md) 0" }}>
              Turn search traffic into your best sales channel.
            </h1>
            <p style={{ font: "var(--font-body-lg)", color: "var(--gray-300)", margin: "0 0 var(--space-lg)", maxWidth: "520px" }}>
              Oneweblink runs SEO, paid, and content programs for ambitious B2B teams across the US, UK, Australia, Canada and the UAE — built for pipeline, not vanity rank checks.
            </p>
            <div style={{ display: "flex", alignItems: "center", gap: "var(--space-sm)", marginBottom: "var(--space-lg)", flexWrap: "wrap" }}>
              <Button variant="primary" size="lg" onClick={() => setDialogOpen(true)}>
                Book a Free Consultation
              </Button>
              <a
                href="#process"
                aria-label="See how we work"
                style={{
                  width: "48px",
                  height: "48px",
                  borderRadius: "50%",
                  border: "1px solid rgba(255,255,255,0.3)",
                  display: "flex",
                  alignItems: "center",
                  justifyContent: "center",
                  flexShrink: 0,
                }}
              >
                <Icon name="arrow-down" size={20} style={{ color: "var(--white)" }} />
              </a>
            </div>
            <div style={{ display: "flex", gap: "10px", flexWrap: "wrap" }}>
              {COUNTRIES.map((c) => (
                <Badge key={c} tone="inverse">
                  {c}
                </Badge>
              ))}
            </div>
          </div>

          <div
            style={{
              borderRadius: "var(--radius-lg)",
              border: "1px solid rgba(255,255,255,0.12)",
              boxShadow: "var(--shadow-xl)",
              overflow: "hidden",
              background: "var(--color-bg)",
            }}
          >
            <div style={{ display: "flex", gap: "6px", padding: "12px var(--space-md)", borderBottom: "1px solid var(--color-border)" }}>
              <span style={{ width: "10px", height: "10px", borderRadius: "50%", background: "var(--gray-300)" }} />
              <span style={{ width: "10px", height: "10px", borderRadius: "50%", background: "var(--gray-300)" }} />
              <span style={{ width: "10px", height: "10px", borderRadius: "50%", background: "var(--gray-300)" }} />
            </div>
            <div style={{ padding: "var(--space-lg)", background: "linear-gradient(160deg, var(--color-accent-tint-weak), var(--color-bg))" }}>
              <p
                style={{
                  font: "var(--font-eyebrow)",
                  color: "var(--color-accent-dark)",
                  textTransform: "uppercase",
                  letterSpacing: "0.06em",
                  margin: "0 0 var(--space-sm)",
                }}
              >
                Live Rank Tracking
              </p>
              <svg viewBox="0 0 320 120" width="100%" height="120" style={{ display: "block", marginBottom: "var(--space-md)" }}>
                <polyline
                  points="0,100 40,90 80,95 120,70 160,60 200,45 240,50 280,20 320,10"
                  fill="none"
                  stroke="var(--color-accent)"
                  strokeWidth="3"
                  strokeLinecap="round"
                  strokeLinejoin="round"
                />
                <polyline
                  points="0,100 40,90 80,95 120,70 160,60 200,45 240,50 280,20 320,10 320,120 0,120"
                  fill="var(--color-accent-tint-weak)"
                  stroke="none"
                  opacity="0.6"
                />
              </svg>
              <div style={{ display: "grid", gridTemplateColumns: "repeat(3, 1fr)", gap: "var(--space-sm)" }}>
                <div style={{ background: "var(--color-bg)", border: "1px solid var(--color-border)", borderRadius: "var(--radius-sm)", padding: "var(--space-sm)" }}>
                  <div style={{ font: "var(--weight-bold) var(--text-xl)/1 var(--font-sans)", color: "var(--color-accent-dark)" }}>#2</div>
                  <div style={{ font: "var(--font-body-sm)", color: "var(--color-text-secondary)" }}>Avg. keyword rank</div>
                </div>
                <div style={{ background: "var(--color-bg)", border: "1px solid var(--color-border)", borderRadius: "var(--radius-sm)", padding: "var(--space-sm)" }}>
                  <div style={{ font: "var(--weight-bold) var(--text-xl)/1 var(--font-sans)", color: "var(--color-accent-dark)" }}>+68%</div>
                  <div style={{ font: "var(--font-body-sm)", color: "var(--color-text-secondary)" }}>Organic sessions</div>
                </div>
                <div style={{ background: "var(--color-bg)", border: "1px solid var(--color-border)", borderRadius: "var(--radius-sm)", padding: "var(--space-sm)" }}>
                  <div style={{ font: "var(--weight-bold) var(--text-xl)/1 var(--font-sans)", color: "var(--color-accent-dark)" }}>92</div>
                  <div style={{ font: "var(--font-body-sm)", color: "var(--color-text-secondary)" }}>Site health score</div>
                </div>
              </div>
            </div>
          </div>
        </div>

        {/* mission statement */}
        <div style={{ maxWidth: "820px", margin: "0 auto", padding: "var(--space-3xl) var(--container-padding) var(--space-2xl)", textAlign: "center", position: "relative" }}>
          <h2 style={{ font: "var(--font-h1)", color: "var(--white)", margin: "0 0 var(--space-md)" }}>
            A Growth Team for High-Impact, High-Performance SEO
          </h2>
          <p style={{ font: "var(--font-body-lg)", color: "var(--gray-300)", margin: 0 }}>
            We&rsquo;re a digital marketing team focused on one thing: building organic and paid programs that deliver real
            pipeline. From technical SEO and content to Google Ads and local search, our specialists help ambitious teams
            across the US, UK, Australia, Canada, UAE and New Zealand grow online.
          </p>
        </div>

        {/* certifications */}
        <div style={{ maxWidth: "var(--container-max)", margin: "0 auto", padding: "0 var(--container-padding) var(--space-3xl)", textAlign: "center", position: "relative" }}>
          <p
            style={{
              font: "var(--font-eyebrow)",
              color: "var(--gray-600)",
              textTransform: "uppercase",
              letterSpacing: "0.08em",
              margin: "0 0 var(--space-lg)",
            }}
          >
            Certified Partners &amp; Platforms
          </p>
          <div style={{ display: "flex", justifyContent: "center", gap: "var(--space-xl)", flexWrap: "wrap" }}>
            {CERTIFICATIONS.map((c) => (
              <span key={c} style={{ font: "var(--weight-semibold) var(--text-lg)/1 var(--font-sans)", color: "var(--gray-300)" }}>
                {c}
              </span>
            ))}
          </div>
        </div>
      </section>

      {/* VERIFIED TRACK RECORD */}
      <section style={{ padding: "var(--space-2xl) 0" }}>
        <div style={{ maxWidth: "var(--container-max)", margin: "0 auto", padding: "0 var(--container-padding)" }}>
          <p
            style={{
              font: "var(--font-eyebrow)",
              color: "var(--color-accent-dark)",
              textTransform: "uppercase",
              letterSpacing: "0.06em",
              margin: "0 0 var(--space-md)",
              textAlign: "center",
            }}
          >
            Verified Track Record
          </p>
          <div className="grid-4" style={{ display: "grid", gridTemplateColumns: "repeat(4, 1fr)", gap: "var(--space-md)" }}>
            {TRACK_RECORD.map((t) => (
              <TrackRecordCard key={t.title} {...t} />
            ))}
          </div>
        </div>
      </section>

      {/* WHY US */}
      <section id="why-us" style={{ padding: "var(--space-3xl) 0" }}>
        <div style={{ maxWidth: "var(--container-max)", margin: "0 auto", padding: "0 var(--container-padding)" }}>
          <div style={{ textAlign: "center", maxWidth: "700px", margin: "0 auto var(--space-2xl)" }}>
            <p style={{ font: "var(--font-eyebrow)", color: "var(--color-accent-dark)", textTransform: "uppercase", letterSpacing: "0.06em", margin: "0 0 8px" }}>
              Why Oneweblink
            </p>
            <h2 style={{ font: "var(--font-h2)", color: "var(--color-text-primary)", margin: "0 0 8px" }}>
              Marketing that reports to revenue, not vanity metrics
            </h2>
            <p style={{ font: "var(--font-body-lg)", color: "var(--color-text-secondary)", margin: 0 }}>
              Four reasons growth teams switch to us and stay.
            </p>
          </div>
          <div className="grid-2" style={{ display: "grid", gridTemplateColumns: "0.9fr 1.1fr", gap: "var(--space-2xl)", alignItems: "center" }}>
            <div style={{ display: "flex", flexDirection: "column", gap: "var(--space-xl)" }}>
              {WHY_BLOCKS.map((w) => (
                <div key={w.title}>
                  <h3 style={{ font: "var(--font-h4)", color: "var(--color-text-primary)", margin: "0 0 6px" }}>{w.title}</h3>
                  <p style={{ font: "var(--font-body)", color: "var(--color-text-secondary)", margin: 0 }}>{w.body}</p>
                </div>
              ))}
            </div>
            <div
              style={{
                aspectRatio: "4/5",
                borderRadius: "var(--radius-lg)",
                border: "1px solid var(--color-border)",
                background: "linear-gradient(160deg, var(--color-accent-tint-weak), var(--color-bg-subtle))",
                display: "flex",
                alignItems: "center",
                justifyContent: "center",
                position: "relative",
                overflow: "hidden",
              }}
            >
              <svg viewBox="0 0 300 360" width="70%" style={{ display: "block" }}>
                <circle cx="150" cy="120" r="90" fill="none" stroke="var(--color-accent)" strokeWidth="2" strokeDasharray="6 8" opacity="0.5" />
                <circle cx="150" cy="120" r="46" fill="var(--color-bg)" stroke="var(--color-accent)" strokeWidth="2" />
                <path d="M150 100 v40 M150 100 l14 8" stroke="var(--color-accent-dark)" strokeWidth="4" strokeLinecap="round" fill="none" />
                <rect x="40" y="230" width="90" height="60" rx="10" fill="var(--color-bg)" stroke="var(--color-accent)" strokeWidth="2" />
                <path d="M56 250 h58 M56 264 h40" stroke="var(--color-accent)" strokeWidth="3" strokeLinecap="round" />
                <rect x="170" y="250" width="90" height="70" rx="10" fill="var(--color-bg)" stroke="var(--color-accent)" strokeWidth="2" />
                <circle cx="195" cy="275" r="9" fill="var(--color-accent-tint)" />
                <path d="M188 296 q7 -14 22 0" stroke="var(--color-accent)" strokeWidth="2.5" fill="none" />
                <path d="M220 270 h24 M220 282 h16" stroke="var(--color-accent)" strokeWidth="2.5" strokeLinecap="round" />
              </svg>
              <span style={{ position: "absolute", bottom: "var(--space-md)", left: 0, right: 0, textAlign: "center", font: "var(--font-body-sm)", color: "var(--color-accent-dark)" }}>
                A strategy call, wherever you are
              </span>
            </div>
          </div>
        </div>
      </section>

      {/* SERVICES */}
      <section id="services" style={{ background: "var(--color-bg-subtle)", padding: "var(--space-3xl) 0" }}>
        <div style={{ maxWidth: "var(--container-max)", margin: "0 auto", padding: "0 var(--container-padding)" }}>
          <div style={{ textAlign: "center", maxWidth: "700px", margin: "0 auto var(--space-lg)" }}>
            <p style={{ font: "var(--font-eyebrow)", color: "var(--color-accent-dark)", textTransform: "uppercase", letterSpacing: "0.06em", margin: "0 0 8px" }}>
              What We Do
            </p>
            <h2 style={{ font: "var(--font-h2)", color: "var(--color-text-primary)", margin: "0 0 8px" }}>Our Growth Services</h2>
            <p style={{ font: "var(--font-body-lg)", color: "var(--color-text-secondary)", margin: 0 }}>
              Four pillars, eleven disciplines — every engagement starts with an audit to find the right mix.
            </p>
          </div>
          <div className="grid-2" style={{ display: "grid", gridTemplateColumns: "repeat(2, 1fr)", gap: "var(--space-lg)" }}>
            {SERVICES.map((s) => (
              <Link key={s.number} href={`/services/${s.slug}`} style={{ textDecoration: "none", color: "inherit" }}>
                <ServiceCard {...s} />
              </Link>
            ))}
          </div>
          <div style={{ textAlign: "center", marginTop: "var(--space-lg)" }}>
            <Button variant="secondary" href="/services">
              View All Services
            </Button>
          </div>
        </div>
      </section>

      {/* INDUSTRIES */}
      <section id="industries" style={{ padding: "var(--space-3xl) 0" }}>
        <div style={{ maxWidth: "var(--container-max)", margin: "0 auto", padding: "0 var(--container-padding)" }}>
          <div style={{ textAlign: "center", maxWidth: "700px", margin: "0 auto var(--space-2xl)" }}>
            <p style={{ font: "var(--font-eyebrow)", color: "var(--color-accent-dark)", textTransform: "uppercase", letterSpacing: "0.06em", margin: "0 0 8px" }}>
              Who We Serve
            </p>
            <h2 style={{ font: "var(--font-h2)", color: "var(--color-text-primary)", margin: "0 0 8px" }}>Industries We Serve Internationally</h2>
            <p style={{ font: "var(--font-body-lg)", color: "var(--color-text-secondary)", margin: 0 }}>
              Different industries need different search strategy. Here&rsquo;s where we go deep.
            </p>
          </div>
          <div className="grid-3" style={{ display: "grid", gridTemplateColumns: "repeat(3, 1fr)", gap: "var(--space-lg)" }}>
            {INDUSTRIES.slice(0, 3).map((i) => (
              <IndustryCard key={i.title} {...i} />
            ))}
          </div>
          <div
            className="grid-2"
            style={{ display: "grid", gridTemplateColumns: "repeat(2, 1fr)", gap: "var(--space-lg)", maxWidth: "760px", margin: "var(--space-lg) auto 0" }}
          >
            {INDUSTRIES.slice(3).map((i) => (
              <IndustryCard key={i.title} {...i} />
            ))}
          </div>
        </div>
      </section>

      {/* PROCESS */}
      <section id="process" style={{ background: "var(--color-bg-inverse)", padding: "var(--space-3xl) 0" }}>
        <div
          className="grid-2"
          style={{ maxWidth: "var(--container-max)", margin: "0 auto", padding: "0 var(--container-padding)", display: "grid", gridTemplateColumns: "0.8fr 1.2fr", gap: "var(--space-2xl)" }}
        >
          <div>
            <p style={{ font: "var(--font-eyebrow)", color: "var(--blue-500)", textTransform: "uppercase", letterSpacing: "0.06em", margin: "0 0 8px" }}>
              How We Work
            </p>
            <h2 style={{ font: "var(--font-h2)", color: "var(--white)", margin: 0 }}>A process built for accountability</h2>
            <p style={{ font: "var(--font-body)", color: "var(--gray-300)", margin: "var(--space-sm) 0 0" }}>
              Success doesn&rsquo;t happen by accident — every engagement follows the same four stages.
            </p>
          </div>
          <div>
            {PROCESS_STEPS.map((s, i) => (
              <ProcessStep key={s.number} {...s} isLast={i === PROCESS_STEPS.length - 1} />
            ))}
          </div>
        </div>
      </section>

      {/* RESULTS */}
      <section id="results" style={{ padding: "var(--space-2xl) 0", background: "var(--color-accent-tint-weak)" }}>
        <div
          className="grid-4"
          style={{ maxWidth: "var(--container-max)", margin: "0 auto", padding: "0 var(--container-padding)", display: "grid", gridTemplateColumns: "repeat(4, 1fr)", gap: "var(--space-lg)" }}
        >
          {RESULTS.map((r) => (
            <StatCounter key={r.label} {...r} />
          ))}
        </div>
      </section>

      {/* TESTIMONIALS */}
      <section style={{ padding: "var(--space-3xl) 0" }}>
        <div
          className="grid-2"
          style={{ maxWidth: "var(--container-max)", margin: "0 auto", padding: "0 var(--container-padding)", display: "grid", gridTemplateColumns: "0.7fr 1.3fr", gap: "var(--space-2xl)" }}
        >
          <div>
            <p style={{ font: "var(--font-eyebrow)", color: "var(--color-accent-dark)", textTransform: "uppercase", letterSpacing: "0.06em", margin: "0 0 8px" }}>
              Client Results
            </p>
            <h2 style={{ font: "var(--font-h2)", color: "var(--color-text-primary)", margin: 0 }}>Don&rsquo;t take our word for it</h2>
            <div style={{ marginTop: "var(--space-md)" }}>
              <Button variant="ghost" href="/portfolio">
                See full case studies <Icon name="arrow-up-right" size={14} />
              </Button>
            </div>
          </div>
          <div>
            {TESTIMONIALS.map((t, i) => (
              <TestimonialRow key={t.name} number={String(i + 1).padStart(2, "0")} {...t} isLast={i === TESTIMONIALS.length - 1} />
            ))}
          </div>
        </div>
      </section>

      {/* FAQ */}
      <section id="faq" style={{ background: "var(--color-bg-subtle)", padding: "var(--space-3xl) 0" }}>
        <div style={{ maxWidth: "var(--container-max)", margin: "0 auto", padding: "0 var(--container-padding)" }}>
          <div style={{ textAlign: "center", maxWidth: "600px", margin: "0 auto var(--space-2xl)" }}>
            <p style={{ font: "var(--font-eyebrow)", color: "var(--color-accent-dark)", textTransform: "uppercase", letterSpacing: "0.06em", margin: "0 0 8px" }}>
              FAQ
            </p>
            <h2 style={{ font: "var(--font-h2)", color: "var(--color-text-primary)", margin: 0 }}>Questions, answered</h2>
          </div>
          <div className="grid-2" style={{ display: "grid", gridTemplateColumns: "repeat(2, 1fr)", gap: "0 var(--space-2xl)" }}>
            {FAQS.map((f, i) => (
              <FaqItem key={f.q} q={f.q} a={f.a} open={openFaq === i} onToggle={() => setOpenFaq(openFaq === i ? -1 : i)} />
            ))}
          </div>
          <div style={{ textAlign: "center", marginTop: "var(--space-lg)" }}>
            <Button variant="ghost" href="/faq">
              View all FAQs <Icon name="arrow-up-right" size={14} />
            </Button>
          </div>
        </div>
      </section>

      {/* FINAL CTA */}
      <section style={{ padding: "var(--space-3xl) var(--container-padding)", textAlign: "center", background: "var(--color-bg-inverse)", position: "relative", overflow: "hidden" }}>
        <div
          style={{
            position: "absolute",
            bottom: "-140px",
            left: "50%",
            transform: "translateX(-50%)",
            width: "480px",
            height: "480px",
            borderRadius: "50%",
            background: "radial-gradient(circle at 50% 30%, var(--blue-500), transparent 70%)",
            opacity: 0.35,
          }}
        />
        <h2 style={{ font: "var(--font-h1)", color: "var(--white)", margin: "0 0 8px", position: "relative" }}>Ready to outrank your competitors?</h2>
        <p style={{ font: "var(--font-body-lg)", color: "var(--gray-300)", margin: "0 0 var(--space-lg)", position: "relative" }}>
          Get a free, no-obligation SEO audit and a 90-day growth roadmap.
        </p>
        <div style={{ display: "flex", gap: "var(--space-sm)", justifyContent: "center", marginBottom: "var(--space-lg)", position: "relative", flexWrap: "wrap" }}>
          <Button variant="primary" size="lg" onClick={() => setDialogOpen(true)}>
            Book Free Consultation
          </Button>
          <Button variant="secondary" size="lg" href={`tel:${SITE.phoneHref}`}>
            Call {SITE.phone}
          </Button>
        </div>
        <p style={{ font: "var(--font-body-sm)", color: "var(--gray-600)", margin: 0, position: "relative" }}>
          {SITE.email} &middot; WhatsApp {SITE.phone} &middot; {SITE.address}
        </p>
      </section>

      <Dialog open={dialogOpen} title="Book Your Free Consultation" onClose={() => setDialogOpen(false)}>
        <ConsultationForm source="home-dialog" onSuccess={() => setTimeout(() => setDialogOpen(false), 1600)} />
      </Dialog>
    </div>
  );
}
