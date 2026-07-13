import Link from "next/link";
import { notFound } from "next/navigation";
import Tag from "@/components/ui/Tag";
import Button from "@/components/ui/Button";
import { BLOG_POSTS, getPostBySlug } from "@/data/blog";

export function generateStaticParams() {
  return BLOG_POSTS.map((p) => ({ slug: p.slug }));
}

export async function generateMetadata({ params }) {
  const { slug } = await params;
  const post = getPostBySlug(slug);
  if (!post) return {};
  return {
    title: post.title,
    description: post.excerpt,
  };
}

function formatDate(iso) {
  return new Date(iso).toLocaleDateString("en-US", { year: "numeric", month: "long", day: "numeric" });
}

export default async function BlogPostPage({ params }) {
  const { slug } = await params;
  const post = getPostBySlug(slug);
  if (!post) notFound();

  const jsonLd = {
    "@context": "https://schema.org",
    "@type": "BlogPosting",
    headline: post.title,
    datePublished: post.date,
    author: { "@type": "Person", name: post.author },
    description: post.excerpt,
  };

  const otherPosts = BLOG_POSTS.filter((p) => p.slug !== post.slug).slice(0, 3);

  return (
    <div style={{ fontFamily: "var(--font-sans)" }}>
      <script type="application/ld+json" dangerouslySetInnerHTML={{ __html: JSON.stringify(jsonLd) }} />

      <article>
        <section style={{ background: "var(--color-bg-inverse)", padding: "var(--space-3xl) var(--container-padding)" }}>
          <div style={{ maxWidth: "760px", margin: "0 auto" }}>
            <Link href="/blog" style={{ font: "var(--font-label)", color: "var(--blue-500)", textDecoration: "none" }}>
              &larr; All Posts
            </Link>
            <div style={{ margin: "var(--space-md) 0 var(--space-sm)" }}>
              <Tag>{post.category}</Tag>
            </div>
            <h1 style={{ font: "var(--font-h1)", color: "var(--white)", margin: "0 0 var(--space-sm)" }}>{post.title}</h1>
            <p style={{ font: "var(--font-body-sm)", color: "var(--gray-300)", margin: 0 }}>
              {formatDate(post.date)} &middot; {post.readTime} &middot; {post.author}
            </p>
          </div>
        </section>

        <section style={{ padding: "var(--space-3xl) 0" }}>
          <div style={{ maxWidth: "760px", margin: "0 auto", padding: "0 var(--container-padding)", display: "flex", flexDirection: "column", gap: "var(--space-md)" }}>
            {post.content.map((paragraph, i) => (
              <p key={i} style={{ font: "var(--font-body-lg)", color: "var(--color-text-primary)", margin: 0 }}>
                {paragraph}
              </p>
            ))}
          </div>
        </section>
      </article>

      <section style={{ padding: "var(--space-2xl) var(--container-padding)", textAlign: "center", background: "var(--color-bg-subtle)" }}>
        <h2 style={{ font: "var(--font-h2)", color: "var(--color-text-primary)", margin: "0 0 8px" }}>Want strategy tailored to your site?</h2>
        <Button variant="primary" size="lg" href="/contact">
          Book a Free Consultation
        </Button>
      </section>

      <section style={{ padding: "var(--space-2xl) 0" }}>
        <div style={{ maxWidth: "var(--container-max)", margin: "0 auto", padding: "0 var(--container-padding)" }}>
          <h2 style={{ font: "var(--font-h3)", color: "var(--color-text-primary)", margin: "0 0 var(--space-md)" }}>More from the blog</h2>
          <div style={{ display: "flex", gap: "var(--space-sm)", flexWrap: "wrap" }}>
            {otherPosts.map((p) => (
              <Link
                key={p.slug}
                href={`/blog/${p.slug}`}
                style={{
                  padding: "10px 18px",
                  border: "1px solid var(--color-border)",
                  borderRadius: "var(--radius-pill)",
                  color: "var(--color-text-primary)",
                  textDecoration: "none",
                  font: "var(--font-label)",
                }}
              >
                {p.title}
              </Link>
            ))}
          </div>
        </div>
      </section>
    </div>
  );
}
