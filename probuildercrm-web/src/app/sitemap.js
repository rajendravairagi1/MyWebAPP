import { SITE } from "@/data/site";
import { listPosts } from "@/lib/repositories/posts";

export default function sitemap() {
  const staticRoutes = ["", "/features", "/pricing", "/about", "/blog", "/faq", "/contact", "/privacy-policy", "/terms-of-service"].map(
    (route) => ({
      url: `${SITE.url}${route}`,
      lastModified: new Date(),
    }),
  );

  const blogRoutes = listPosts().map((post) => ({
    url: `${SITE.url}/blog/${post.slug}`,
    lastModified: post.updatedAt,
  }));

  return [...staticRoutes, ...blogRoutes];
}
