import { SITE } from "@/data/site";
import { SERVICES } from "@/data/services";
import { INDUSTRIES } from "@/data/industries";
import { BLOG_POSTS } from "@/data/blog";
import { CASE_STUDIES } from "@/data/portfolio";

export default function sitemap() {
  const staticRoutes = ["", "/services", "/industries", "/about", "/portfolio", "/blog", "/contact", "/faq"].map((route) => ({
    url: `${SITE.url}${route}`,
    lastModified: new Date(),
  }));

  const serviceRoutes = SERVICES.map((s) => ({ url: `${SITE.url}/services/${s.slug}`, lastModified: new Date() }));
  const industryRoutes = INDUSTRIES.map((i) => ({ url: `${SITE.url}/industries/${i.slug}`, lastModified: new Date() }));
  const blogRoutes = BLOG_POSTS.map((p) => ({ url: `${SITE.url}/blog/${p.slug}`, lastModified: p.date }));
  const portfolioRoutes = CASE_STUDIES.map((c) => ({ url: `${SITE.url}/portfolio/${c.slug}`, lastModified: new Date() }));

  return [...staticRoutes, ...serviceRoutes, ...industryRoutes, ...blogRoutes, ...portfolioRoutes];
}
