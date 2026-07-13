import { SITE } from "@/data/site";
import { SERVICES } from "@/data/services";
import { INDUSTRIES } from "@/data/industries";
import { listPosts } from "@/lib/repositories/posts";
import { listCaseStudies } from "@/lib/repositories/caseStudies";

export default function sitemap() {
  const staticRoutes = [
    "",
    "/services",
    "/industries",
    "/about",
    "/portfolio",
    "/blog",
    "/contact",
    "/faq",
    "/privacy-policy",
    "/terms-of-service",
  ].map((route) => ({
    url: `${SITE.url}${route}`,
    lastModified: new Date(),
  }));

  const serviceRoutes = SERVICES.map((s) => ({ url: `${SITE.url}/services/${s.slug}`, lastModified: new Date() }));
  const industryRoutes = INDUSTRIES.map((i) => ({ url: `${SITE.url}/industries/${i.slug}`, lastModified: new Date() }));
  const blogRoutes = listPosts().map((p) => ({ url: `${SITE.url}/blog/${p.slug}`, lastModified: p.updatedAt }));
  const portfolioRoutes = listCaseStudies().map((c) => ({ url: `${SITE.url}/portfolio/${c.slug}`, lastModified: c.updatedAt }));

  return [...staticRoutes, ...serviceRoutes, ...industryRoutes, ...blogRoutes, ...portfolioRoutes];
}
