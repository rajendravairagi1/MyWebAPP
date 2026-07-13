export const SITE = {
  name: "Oneweblink",
  legalName: "Oneweblink Pvt Ltd",
  tagline: "Global SEO & Growth Partner",
  email: "Info@oneweblink.com",
  phone: "+91 78980 02496",
  phoneHref: "+917898002496",
  whatsapp: "https://wa.me/917898002496",
  address: "E-14 Amrapali Township, Sonwa, Indore",
  url: "https://www.oneweblink.com",
  social: {
    linkedin: "https://www.linkedin.com/company/oneweblink/",
    instagram: "#",
    facebook: "#",
  },
};

export const NAV_ITEMS = [
  { label: "Services", href: "/services" },
  { label: "Industries", href: "/industries" },
  { label: "About", href: "/about" },
  { label: "Work", href: "/portfolio" },
  { label: "Blog", href: "/blog" },
  { label: "Contact", href: "/contact" },
];

export const FOOTER_COLUMNS = [
  {
    heading: "Services",
    links: [
      { label: "SEO & Content Marketing", href: "/services/seo-content-marketing" },
      { label: "Local Search & Reputation", href: "/services/local-search-reputation" },
      { label: "Paid & Social Growth", href: "/services/paid-social-growth" },
      { label: "Web & Emerging Search", href: "/services/web-emerging-search" },
    ],
  },
  {
    heading: "Company",
    links: [
      { label: "About Us", href: "/about" },
      { label: "Industries", href: "/industries" },
      { label: "Our Work", href: "/portfolio" },
      { label: "Blog", href: "/blog" },
      { label: "FAQ", href: "/faq" },
    ],
  },
  {
    heading: "Contact",
    links: [
      { label: SITE.email, href: `mailto:${SITE.email}` },
      { label: SITE.phone, href: `tel:${SITE.phoneHref}` },
      { label: SITE.address, href: "/contact" },
    ],
  },
];

export const COUNTRIES = ["USA", "UK", "Australia", "Canada", "UAE", "New Zealand"];

export const CERTIFICATIONS = [
  "Google Partner",
  "Google Ads Certified",
  "Meta Business Partner",
  "WordPress",
  "SEMrush Certified",
  "Ahrefs Certified",
];

export const TRACK_RECORD = [
  {
    kind: "star",
    title: "Oneweblink Pvt Ltd",
    subtitle: "Upwork Agency · Since 2014",
    stat: "63 Jobs Completed · $10K+ Earned",
    href: "https://www.upwork.com/agencies/469527783214243840/",
    cta: "View Agency on Upwork",
  },
  {
    kind: "star",
    title: "Sanjay B.",
    subtitle: "SEO Specialist · Indore, India",
    stat: "4.7 Rating (66 Reviews) · 81 Jobs · 100% Job Success",
    href: "https://www.upwork.com/freelancers/~0136cf463d86863fdc",
    cta: "View Profile on Upwork",
  },
  {
    kind: "in",
    title: "Oneweblink",
    subtitle: "Company Page",
    stat: "Follow us for updates & case studies",
    href: "https://www.linkedin.com/company/oneweblink/",
    cta: "View on LinkedIn",
  },
  {
    kind: "in",
    title: "Rajendra Vairagi",
    subtitle: "Founder, Oneweblink",
    stat: "Recommended by clients on LinkedIn",
    href: "https://www.linkedin.com/in/rajendravairagi/",
    cta: "View on LinkedIn",
  },
];
