export const SERVICES = [
  {
    slug: "seo-content-marketing",
    number: "01",
    title: "SEO & Content Marketing",
    icon: "search",
    description: "Full-funnel organic strategy across technical, content, and authority signals.",
    longDescription:
      "We treat SEO as a revenue channel, not a checklist. Every engagement starts with a technical and content audit, then moves into a prioritized roadmap that fixes what's blocking rankings today and builds the content and authority that compounds over the next 12 months.",
    bullets: [
      { h: "On-Page & Technical SEO", d: "Site speed, crawlability, and structured data fixed at the source." },
      { h: "Content Marketing", d: "Search-driven content that ranks and reads like it was written for humans." },
      { h: "Link Building", d: "Editorial links from relevant, authoritative publishers — no shortcuts." },
      { h: "Keyword & Competitor Research", d: "A clear map of the terms worth ranking for, and who you're beating." },
    ],
    deliverables: [
      "Technical SEO audit & fix roadmap",
      "Keyword & competitor research",
      "On-page optimization for priority pages",
      "Editorial content calendar & production",
      "Authority link building campaign",
      "Monthly rankings & traffic reporting",
    ],
  },
  {
    slug: "local-search-reputation",
    number: "02",
    title: "Local Search & Reputation",
    icon: "map-pin",
    description: "Win the map pack and turn nearby searches into foot traffic and calls.",
    longDescription:
      "For multi-location and local-service businesses, the map pack is where deals are won or lost. We optimize your Google Business Profiles, build consistent citations, and run a steady review program so you show up first when it matters.",
    bullets: [
      { h: "Local SEO", d: "Citation building and local keyword targeting for every market you serve." },
      { h: "Google Business Profile", d: "Optimized listings, posts, and Q&A that convert local searchers." },
      { h: "Review Management", d: "A steady, authentic flow of reviews across your key locations." },
      { h: "Map Pack Ranking", d: "Ongoing optimization to keep you in the top three local results." },
    ],
    deliverables: [
      "Google Business Profile optimization",
      "Local citation audit & cleanup",
      "Review generation & response program",
      "Location page SEO",
      "Map pack rank tracking",
      "Monthly local visibility report",
    ],
  },
  {
    slug: "paid-social-growth",
    number: "03",
    title: "Paid & Social Growth",
    icon: "target",
    description: "Paid and organic programs tuned for pipeline, not just reach.",
    longDescription:
      "Paid media should shorten your sales cycle, not just inflate impressions. We build Google Ads and social programs tied to cost-per-acquisition targets, layered with retargeting and a content calendar that keeps your brand present between clicks.",
    bullets: [
      { h: "Google Ads", d: "Search and shopping campaigns tuned for cost-per-acquisition." },
      { h: "Social Media Marketing", d: "Organic and paid programs that build audience and demand." },
      { h: "Retargeting Campaigns", d: "Bring warm visitors back with sequenced, relevant offers." },
      { h: "Content Calendars", d: "A consistent publishing cadence across every active channel." },
    ],
    deliverables: [
      "Google Ads search & shopping campaigns",
      "Paid social campaign management",
      "Retargeting funnel setup",
      "Organic social content calendar",
      "Landing page conversion review",
      "Monthly spend & CPA reporting",
    ],
  },
  {
    slug: "web-emerging-search",
    number: "04",
    title: "Web & Emerging Search",
    icon: "code-2",
    description: "Fast infrastructure and visibility on the search surfaces of tomorrow.",
    longDescription:
      "Rankings mean nothing on a slow, unmaintained site — and search itself is expanding past the blue links. We build fast WordPress infrastructure, optimize app store listings, and structure content to get cited by AI assistants like ChatGPT and Gemini.",
    bullets: [
      { h: "WordPress Development", d: "Fast, SEO-ready builds with ongoing technical support." },
      { h: "App Store Optimization (ASO)", d: "Keyword ranking and listing optimization for iOS and Android." },
      { h: "GEO Content Marketing", d: "Content structured to get cited by AI search engines like ChatGPT and Gemini." },
    ],
    deliverables: [
      "WordPress build or migration",
      "Core Web Vitals & speed optimization",
      "App Store Optimization (ASO)",
      "Generative Engine Optimization (GEO) content",
      "Ongoing technical support & maintenance",
      "Monthly performance reporting",
    ],
  },
];

export function getServiceBySlug(slug) {
  return SERVICES.find((s) => s.slug === slug);
}
