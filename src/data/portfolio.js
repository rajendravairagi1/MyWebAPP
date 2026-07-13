export const CASE_STUDIES = [
  {
    slug: "northbridge-saas-organic-pipeline",
    client: "Northbridge SaaS",
    industry: "SaaS & Technology",
    location: "Austin, USA",
    title: "Turning Organic Search into a B2B SaaS's Largest Pipeline Source",
    summary: "A technical SEO overhaul and content program that took organic from an afterthought to the top of the funnel.",
    stats: [
      { value: "220%", label: "Organic traffic growth" },
      { value: "6", label: "Months to top pipeline source" },
      { value: "3.4x", label: "Demo requests from organic" },
    ],
    challenge:
      "Northbridge's product pages were technically sound but invisible — thin content, no keyword strategy, and a blog that hadn't been updated in over a year.",
    approach:
      "We ran a full technical and content audit, rebuilt the information architecture around buyer-stage keyword clusters, and shipped a weekly content and internal linking cadence backed by targeted link building.",
    outcome:
      "Within six months, organic became the largest source of demo requests, ahead of both paid and outbound — with compounding gains that kept accelerating past the engagement's first year.",
  },
  {
    slug: "anand-dental-group-map-pack",
    client: "Anand Dental Group",
    industry: "Healthcare & Medical",
    location: "Sydney, Australia",
    title: "From Page Three to the Map Pack in Eight Weeks",
    summary: "A local SEO sprint across five clinic locations that fixed citation chaos and rebuilt review velocity.",
    stats: [
      { value: "8 weeks", label: "To map pack visibility" },
      { value: "5", label: "Locations optimized" },
      { value: "4.8★", label: "Average rating after program" },
    ],
    challenge:
      "Five clinic locations had inconsistent NAP data across directories and Google Business Profiles that hadn't been claimed or optimized, leaving the group invisible in local map results.",
    approach:
      "We audited and corrected citations across every location, rebuilt each Google Business Profile with accurate categories and services, and stood up a compliant, automated review request flow.",
    outcome:
      "All five locations moved into the map pack for their primary specialty terms within eight weeks, with review volume and average rating both climbing steadily since.",
  },
  {
    slug: "whitfield-co-authority-content",
    client: "Whitfield & Co",
    industry: "Professional & Legal Services",
    location: "London, UK",
    title: "Building Board-Level Trust with Plain-English SEO Reporting",
    summary: "A content authority program paired with reporting the firm's board could actually understand and act on.",
    stats: [
      { value: "3x", label: "Qualified inquiry growth" },
      { value: "40+", label: "Ranking practice-area pages" },
      { value: "100%", label: "Reports read by the board" },
    ],
    challenge:
      "Whitfield & Co had strong expertise but a website that read like a directory listing, and no way to explain SEO progress to a board skeptical of digital marketing spend.",
    approach:
      "We built practice-area content hubs backed by case results and credentials, then replaced vague vanity-metric reports with a plain-English monthly summary tied directly to inquiry volume.",
    outcome:
      "Qualified inquiries tripled within the year, and the monthly report became a standing agenda item the board actually reads — settling the internal debate about whether SEO spend was working.",
  },
];

export function getCaseStudyBySlug(slug) {
  return CASE_STUDIES.find((c) => c.slug === slug);
}
