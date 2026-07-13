export const INDUSTRIES = [
  {
    slug: "e-commerce-retail",
    icon: "shopping-cart",
    title: "E-Commerce & Retail",
    description: "Product-page SEO, feed optimization, and paid campaigns built to move inventory.",
    longDescription:
      "Online retail lives and dies on category and product-page visibility. We optimize your catalog for search and shopping feeds, then layer paid campaigns on top of the SKUs and seasons that matter most.",
    challenges: ["Thin, duplicate product content", "Seasonal demand spikes", "Shopping feed errors suppressing listings"],
    approach: ["Product & category page SEO", "Shopping feed audit & optimization", "Seasonal paid campaign planning"],
  },
  {
    slug: "healthcare-medical",
    icon: "stethoscope",
    title: "Healthcare & Medical",
    description: "Compliant, patient-first content and local visibility for clinics and practices.",
    longDescription:
      "Patients search locally and urgently. We build compliant, plain-language content and local visibility programs that get clinics found by the right patients at the right moment, without cutting corners on accuracy.",
    challenges: ["Strict content compliance requirements", "Multi-location visibility", "Trust signals for high-stakes decisions"],
    approach: ["Compliant content review process", "Google Business Profile management per location", "Patient review program"],
  },
  {
    slug: "professional-legal-services",
    icon: "scale",
    title: "Professional & Legal Services",
    description: "Authority-building content and local search for firms competing on trust.",
    longDescription:
      "Legal and professional services buyers research extensively before they call. We build the authoritative content and local presence that earns trust before the first conversation happens.",
    challenges: ["Long, trust-driven sales cycles", "Competing against directory sites", "Practice-area content depth"],
    approach: ["Practice-area content hubs", "Local & directory citation strategy", "Case result & credential showcasing"],
  },
  {
    slug: "saas-technology",
    icon: "layout-grid",
    title: "SaaS & Technology",
    description: "Content and technical SEO built for long sales cycles and developer audiences.",
    longDescription:
      "SaaS SEO has to work for both search engines and skeptical technical buyers. We combine technical SEO with content that speaks credibly to developer and decision-maker audiences alike.",
    challenges: ["Competitive, high-CPC keywords", "Technical documentation vs. marketing content", "Long, multi-stakeholder sales cycles"],
    approach: ["Bottom-of-funnel comparison content", "Technical SEO for app-heavy sites", "Integration & use-case landing pages"],
  },
  {
    slug: "real-estate",
    icon: "home",
    title: "Real Estate",
    description: "Listing visibility, local SEO, and lead capture for agents and agencies.",
    longDescription:
      "Real estate search is hyperlocal and time-sensitive. We keep listings visible, local pages ranking, and lead capture tuned so agents spend less time chasing traffic and more time closing.",
    challenges: ["Highly localized competition", "Fast-moving listing inventory", "Lead quality vs. lead volume"],
    approach: ["Neighborhood & listing page SEO", "Local map pack optimization", "Lead capture & follow-up funnel review"],
  },
];

export function getIndustryBySlug(slug) {
  return INDUSTRIES.find((i) => i.slug === slug);
}
