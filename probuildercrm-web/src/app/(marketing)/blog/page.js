import Link from "next/link";
import Tag from "@/components/ui/Tag";
import { listPosts } from "@/lib/repositories/posts";

export const dynamic = "force-dynamic";

export const metadata = {
  title: "Blog",
  description: "Guides and insights for real estate builders and developers — project management, collections, and growing your business.",
  alternates: { canonical: "/blog" },
};

function formatDate(iso) {
  return new Date(iso).toLocaleDateString("en-US", { year: "numeric", month: "long", day: "numeric" });
}

export default function BlogPage() {
  const posts = listPosts();

  return (
    <>
      <section style={{ background: "var(--color-bg-inverse)", padding: "var(--space-3xl) 0", textAlign: "center" }}>
        <div className="container" style={{ maxWidth: 720 }}>
          <p style={{ font: "var(--font-eyebrow)", color: "#a5b4fc", textTransform: "uppercase", letterSpacing: "0.08em", margin: "0 0 12px" }}>
            Insights
          </p>
          <h1 style={{ font: "var(--font-h1)", color: "#fff", margin: "0 0 var(--space-sm)" }}>The Pro Builder CRM Blog</h1>
          <p style={{ font: "var(--font-body-lg)", color: "var(--gray-300)", margin: 0 }}>
            Practical notes on running a real estate business — bookings, collections, and growth.
          </p>
        </div>
      </section>

      <section className="section">
        <div className="container">
          {posts.length === 0 ? (
            <p style={{ textAlign: "center", color: "var(--color-ink-soft)" }}>No posts yet — check back soon.</p>
          ) : (
            <div className="grid-2">
              {posts.map((post) => (
                <Link key={post.slug} href={`/blog/${post.slug}`} className="card" style={{ textDecoration: "none", color: "inherit", display: "flex", flexDirection: "column", gap: 10 }}>
                  <Tag>{post.category}</Tag>
                  <h2 style={{ font: "var(--font-h3)", fontSize: "1.2rem", margin: 0 }}>{post.title}</h2>
                  <p style={{ margin: 0, color: "var(--color-ink-soft)", fontSize: "0.95rem" }}>{post.excerpt}</p>
                  <span style={{ fontSize: "0.85rem", color: "var(--color-ink-soft)", marginTop: "auto" }}>
                    {formatDate(post.date)} · {post.readTime}
                  </span>
                </Link>
              ))}
            </div>
          )}
        </div>
      </section>
    </>
  );
}
