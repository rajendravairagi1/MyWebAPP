import Icon from "@/components/ui/Icon";
import Button from "@/components/ui/Button";
import { WHY_BLOCKS, PROCESS_STEPS } from "@/data/process";
import { SITE } from "@/data/site";

export const metadata = {
  title: "About Us",
  description: "Oneweblink is a growth team based in Indore, India, running SEO, paid, and content programs for teams across the US, UK, Australia, Canada, UAE and New Zealand.",
};

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
              style={{ font: "var(--font-body-sm)", color: "var(--blue-500)", border: "1px solid rgba(59,130,246,0.4)", borderRadius: "var(--radius-pill)", padding: "6px 14px" }}
            >
              {s}
            </span>
          ))}
        </div>
      </div>
    </div>
  );
}

export default function AboutPage() {
  return (
    <div style={{ fontFamily: "var(--font-sans)" }}>
      <section style={{ background: "var(--color-bg-inverse)", padding: "var(--space-3xl) var(--container-padding)", textAlign: "center" }}>
        <p style={{ font: "var(--font-eyebrow)", color: "var(--blue-500)", textTransform: "uppercase", letterSpacing: "0.06em", margin: "0 0 8px" }}>
          About Oneweblink
        </p>
        <h1 style={{ font: "var(--font-h1)", color: "var(--white)", margin: "0 auto var(--space-sm)", maxWidth: "760px" }}>
          A growth team for high-impact, high-performance SEO
        </h1>
        <p style={{ font: "var(--font-body-lg)", color: "var(--gray-300)", margin: "0 auto", maxWidth: "640px" }}>
          Based in Indore, India, and working async across US, UK, Australian, Canadian, UAE and NZ hours, we&rsquo;re a
          specialist team focused on one thing: building organic and paid programs that deliver real pipeline.
        </p>
      </section>

      <section style={{ padding: "var(--space-3xl) 0" }}>
        <div style={{ maxWidth: "var(--container-max)", margin: "0 auto", padding: "0 var(--container-padding)" }}>
          <div style={{ textAlign: "center", maxWidth: "700px", margin: "0 auto var(--space-2xl)" }}>
            <h2 style={{ font: "var(--font-h2)", color: "var(--color-text-primary)", margin: "0 0 8px" }}>
              Marketing that reports to revenue, not vanity metrics
            </h2>
          </div>
          <div className="grid-2" style={{ display: "grid", gridTemplateColumns: "repeat(2, 1fr)", gap: "var(--space-xl)" }}>
            {WHY_BLOCKS.map((w) => (
              <div key={w.title}>
                <h3 style={{ font: "var(--font-h4)", color: "var(--color-text-primary)", margin: "0 0 6px" }}>{w.title}</h3>
                <p style={{ font: "var(--font-body)", color: "var(--color-text-secondary)", margin: 0 }}>{w.body}</p>
              </div>
            ))}
          </div>
        </div>
      </section>

      <section style={{ background: "var(--color-bg-subtle)", padding: "var(--space-3xl) 0" }}>
        <div
          className="grid-2"
          style={{ maxWidth: "var(--container-max)", margin: "0 auto", padding: "0 var(--container-padding)", display: "grid", gridTemplateColumns: "0.9fr 1.1fr", gap: "var(--space-2xl)", alignItems: "center" }}
        >
          <div
            style={{
              aspectRatio: "1/1",
              borderRadius: "var(--radius-lg)",
              border: "1px solid var(--color-border)",
              background: "linear-gradient(160deg, var(--color-accent-tint-weak), var(--color-bg))",
              display: "flex",
              alignItems: "center",
              justifyContent: "center",
            }}
          >
            <div style={{ textAlign: "center" }}>
              <div
                style={{
                  width: "88px",
                  height: "88px",
                  borderRadius: "50%",
                  background: "var(--color-accent-tint)",
                  display: "flex",
                  alignItems: "center",
                  justifyContent: "center",
                  margin: "0 auto var(--space-md)",
                }}
              >
                <Icon name="user" size={36} style={{ color: "var(--color-accent-dark)" }} />
              </div>
              <p style={{ font: "var(--font-h4)", color: "var(--color-text-primary)", margin: "0 0 4px" }}>Rajendra Vairagi</p>
              <p style={{ font: "var(--font-body-sm)", color: "var(--color-text-secondary)", margin: 0 }}>Founder, Oneweblink</p>
            </div>
          </div>
          <div>
            <h2 style={{ font: "var(--font-h2)", color: "var(--color-text-primary)", margin: "0 0 var(--space-sm)" }}>Our story</h2>
            <p style={{ font: "var(--font-body)", color: "var(--color-text-secondary)", margin: "0 0 var(--space-md)" }}>
              Oneweblink started in Indore in 2014 as a freelance SEO practice and grew into a full-stack growth team as
              clients kept asking for more of the channel work behind their rankings — content, paid, local search, and
              now generative-engine visibility.
            </p>
            <p style={{ font: "var(--font-body)", color: "var(--color-text-secondary)", margin: 0 }}>
              What hasn&rsquo;t changed is the way we work: month-to-month engagements, plain-English reporting, and a
              team that treats your rankings as a means to pipeline, not the goal itself.
            </p>
          </div>
        </div>
      </section>

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

      <section style={{ padding: "var(--space-2xl) var(--container-padding)", textAlign: "center" }}>
        <h2 style={{ font: "var(--font-h2)", color: "var(--color-text-primary)", margin: "0 0 8px" }}>Want to work with us?</h2>
        <p style={{ font: "var(--font-body-lg)", color: "var(--color-text-secondary)", margin: "0 0 var(--space-lg)" }}>
          Get a free, no-obligation SEO audit and a 90-day growth roadmap.
        </p>
        <div style={{ display: "flex", gap: "var(--space-sm)", justifyContent: "center", flexWrap: "wrap" }}>
          <Button variant="primary" size="lg" href="/contact">
            Book a Free Consultation
          </Button>
          <Button variant="secondary" size="lg" href={`tel:${SITE.phoneHref}`}>
            Call {SITE.phone}
          </Button>
        </div>
      </section>
    </div>
  );
}
