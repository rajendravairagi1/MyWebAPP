export const BLOG_POSTS = [
  {
    slug: "technical-seo-checklist-2026",
    title: "The 2026 Technical SEO Checklist for Growing Sites",
    excerpt: "The technical fixes that move the needle in 2026 — from Core Web Vitals to structured data to crawl budget.",
    category: "SEO",
    date: "2026-06-02",
    readTime: "7 min read",
    author: "Sanjay B.",
    content: [
      "Technical SEO is the foundation everything else stands on — you can publish the best content in the world, but if search engines can't crawl, render, and understand your site, none of it counts.",
      "Start with crawlability: check your robots.txt, XML sitemaps, and internal linking so every important page is discoverable within a few clicks of the homepage. Then move to rendering — make sure client-side JavaScript isn't hiding critical content from crawlers.",
      "Core Web Vitals still matter. Largest Contentful Paint, Interaction to Next Paint, and Cumulative Layout Shift are ranking signals and, more importantly, they correlate directly with conversion rate. Slow sites lose customers before SEO even enters the picture.",
      "Structured data is your shortcut to richer search results — product, review, FAQ, and article schema all increase the real estate you occupy on the results page. Pair that with a clean URL structure and consistent canonical tags, and you've covered the fundamentals most sites still get wrong.",
    ],
  },
  {
    slug: "local-seo-multi-location-guide",
    title: "A Practical Guide to Local SEO for Multi-Location Businesses",
    excerpt: "How to structure Google Business Profiles, citations, and location pages when you're managing more than one storefront.",
    category: "Local SEO",
    date: "2026-05-18",
    readTime: "6 min read",
    author: "Rajendra Vairagi",
    content: [
      "Multi-location SEO is a different discipline from single-site SEO. Every location needs its own optimized Google Business Profile, its own location page, and its own consistent NAP (name, address, phone) data across the web.",
      "Citation consistency is the unglamorous work that actually moves map pack rankings. A mismatched suite number or an old phone number on a single directory can quietly suppress an otherwise strong listing.",
      "Location pages should be genuinely useful, not templated filler — local team bios, location-specific service notes, and real customer reviews all outperform a copy-pasted city name swapped into a generic template.",
      "Finally, build a review generation process that runs on autopilot: a simple post-visit SMS or email flow, per location, keeps your review velocity healthy without manual chasing.",
    ],
  },
  {
    slug: "geo-ai-search-optimization",
    title: "GEO: Optimizing Content for AI Search Engines",
    excerpt: "ChatGPT, Gemini, and Perplexity are now a real referral source. Here's how to structure content so they cite you.",
    category: "GEO",
    date: "2026-04-27",
    readTime: "5 min read",
    author: "Sanjay B.",
    content: [
      "Generative Engine Optimization (GEO) is the practice of structuring content so AI assistants can extract, understand, and cite it confidently — it's SEO's newest sibling, not a replacement for it.",
      "AI engines favor clear, well-structured answers: direct questions paired with concise, factual answers near the top of the page, followed by supporting detail and evidence.",
      "Citations and specificity build trust with these systems the same way they build trust with readers — original data, named sources, and dated claims all get referenced more often than vague marketing copy.",
      "The sites winning GEO visibility today are the same sites that have always won at content quality — GEO rewards clarity and substance, not keyword density.",
    ],
  },
  {
    slug: "google-ads-cpa-optimization",
    title: "Cutting Google Ads CPA Without Cutting Volume",
    excerpt: "Five levers to pull before you cut your budget — negative keywords, match types, landing pages, and more.",
    category: "Paid Media",
    date: "2026-03-11",
    readTime: "6 min read",
    author: "Rajendra Vairagi",
    content: [
      "When cost-per-acquisition creeps up, the instinct is to cut budget. That's usually the wrong first move — it shrinks volume without fixing the underlying inefficiency.",
      "Start with search term reports: negative keywords remove wasted spend on queries that were never going to convert, often recovering 10-20% of budget immediately.",
      "Match type discipline matters more than most advertisers realize — broad match can work, but only with tight automated bidding and a strong negative keyword list underneath it.",
      "Landing page relevance is the most underrated lever. A landing page that mirrors ad copy and offers a single, clear next action consistently outperforms a generic homepage redirect.",
    ],
  },
];

export function getPostBySlug(slug) {
  return BLOG_POSTS.find((p) => p.slug === slug);
}
