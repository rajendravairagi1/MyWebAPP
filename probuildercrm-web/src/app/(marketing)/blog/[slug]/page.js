import { notFound } from "next/navigation";
import Tag from "@/components/ui/Tag";
import JsonLd from "@/components/seo/JsonLd";
import PostContent from "@/components/blog/PostContent";
import { getPostBySlug } from "@/lib/repositories/posts";
import { articleSchema, breadcrumbSchema } from "@/lib/schema";

export const dynamic = "force-dynamic";

function formatDate(iso) {
  return new Date(iso).toLocaleDateString("en-US", { year: "numeric", month: "long", day: "numeric" });
}

export async function generateMetadata({ params }) {
  const { slug } = await params;
  const post = getPostBySlug(slug);
  if (!post) return {};

  return {
    title: post.title,
    description: post.excerpt,
    alternates: { canonical: `/blog/${post.slug}` },
    openGraph: {
      title: post.title,
      description: post.excerpt,
      type: "article",
      publishedTime: post.date,
    },
  };
}

export default async function BlogPostPage({ params }) {
  const { slug } = await params;
  const post = getPostBySlug(slug);
  if (!post) notFound();

  return (
    <>
      <JsonLd
        data={[
          articleSchema(post),
          breadcrumbSchema([
            { name: "Home", href: "/" },
            { name: "Blog", href: "/blog" },
            { name: post.title, href: `/blog/${post.slug}` },
          ]),
        ]}
      />

      <article>
        <section style={{ background: "var(--color-bg-inverse)", padding: "var(--space-3xl) 0", textAlign: "center" }}>
          <div className="container" style={{ maxWidth: 760 }}>
            <div style={{ display: "flex", justifyContent: "center", marginBottom: 16 }}>
              <Tag>{post.category}</Tag>
            </div>
            <h1 style={{ font: "var(--font-h1)", fontSize: "clamp(1.8rem, 3.5vw, 2.6rem)", color: "#fff", margin: "0 0 var(--space-sm)" }}>
              {post.title}
            </h1>
            <p style={{ color: "var(--gray-300)", margin: 0 }}>
              {post.author} · {formatDate(post.date)} · {post.readTime}
            </p>
          </div>
        </section>

        <section className="section">
          <div className="container" style={{ maxWidth: 720 }}>
            <PostContent blocks={post.content} />
          </div>
        </section>
      </article>
    </>
  );
}
