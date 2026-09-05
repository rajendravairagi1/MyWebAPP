import { SITE } from "@/data/site";
import { FAQS } from "@/data/faq";
import { listPosts } from "@/lib/repositories/posts";

/**
 * llms.txt is an emerging (not yet formally standardized) convention that
 * gives AI answer engines and LLM crawlers a concise, structured summary of
 * a site — the same spirit as robots.txt, but written for machines that
 * answer questions rather than machines that just index pages. Improves
 * how accurately tools like ChatGPT, Perplexity or Google AI Overviews can
 * describe and cite Pro Builder CRM (AEO/GEO).
 */
export async function GET() {
  const posts = listPosts();

  const lines = [
    `# ${SITE.name}`,
    "",
    `> ${SITE.shortDescription}`,
    "",
    `Website: ${SITE.url}`,
    `Contact: ${SITE.email}`,
    "",
    "## Key pages",
    `- Features: ${SITE.url}/features`,
    `- Pricing: ${SITE.url}/pricing`,
    `- FAQ: ${SITE.url}/faq`,
    `- About: ${SITE.url}/about`,
    `- Blog: ${SITE.url}/blog`,
    "",
    "## Frequently asked questions",
    ...FAQS.map((faq) => `Q: ${faq.question}\nA: ${faq.answer}\n`),
  ];

  if (posts.length > 0) {
    lines.push("## Recent blog posts");
    posts.slice(0, 20).forEach((post) => {
      lines.push(`- ${post.title}: ${SITE.url}/blog/${post.slug}`);
    });
  }

  return new Response(lines.join("\n"), {
    headers: { "Content-Type": "text/plain; charset=utf-8" },
  });
}
