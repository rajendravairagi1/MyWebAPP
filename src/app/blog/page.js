import Link from "next/link";
import Tag from "@/components/ui/Tag";
import { BLOG_POSTS } from "@/data/blog";

export const metadata = {
  title: "Blog",
  description: "SEO, local search, GEO, and paid media insights from the Oneweblink team.",
};

function formatDate(iso) {
  return new Date(iso).toLocaleDateString("en-US", { year: "numeric", month: "long", day: "numeric" });
}

export default function BlogPage() {
  return (
    <div style={{ fontFamily: "var(--font-sans)" }}>
      <section style={{ background: "var(--color-bg-inverse)", padding: "var(--space-3xl) var(--container-padding)", textAlign: "center" }}>
        <p style={{ font: "var(--font-eyebrow)", color: "var(--blue-500)", textTransform: "uppercase", letterSpacing: "0.06em", margin: "0 0 8px" }}>
          Insights
        </p>
        <h1 style={{ font: "var(--font-h1)", color: "var(--white)", margin: "0 0 var(--space-sm)" }}>The Oneweblink Blog</h1>
        <p style={{ font: "var(--font-body-lg)", color: "var(--gray-300)", margin: "0 auto", maxWidth: "640px" }}>
          Notes on SEO, local search, GEO, and paid media from the specialists running the campaigns.
        </p>
      </section>

      <section style={{ padding: "var(--space-3xl) 0" }}>
        <div
          className="grid-2"
          style={{ maxWidth: "var(--container-max)", margin: "0 auto", padding: "0 var(--container-padding)", display: "grid", gridTemplateColumns: "repeat(2, 1fr)", gap: "var(--space-lg)" }}
        >
          {BLOG_POSTS.map((p) => (
            <Link
              key={p.slug}
              href={`/blog/${p.slug}`}
              style={{
                display: "flex",
                flexDirection: "column",
                gap: "var(--space-sm)",
                border: "1px solid var(--color-border)",
                borderRadius: "var(--radius-lg)",
                padding: "var(--space-lg)",
                textDecoration: "none",
                color: "inherit",
              }}
            >
              <Tag>{p.category}</Tag>
              <h2 style={{ font: "var(--font-h3)", color: "var(--color-text-primary)", margin: 0 }}>{p.title}</h2>
              <p style={{ font: "var(--font-body)", color: "var(--color-text-secondary)", margin: 0 }}>{p.excerpt}</p>
              <p style={{ font: "var(--font-body-sm)", color: "var(--color-text-secondary)", margin: 0 }}>
                {formatDate(p.date)} &middot; {p.readTime} &middot; {p.author}
              </p>
            </Link>
          ))}
        </div>
      </section>
    </div>
  );
}
