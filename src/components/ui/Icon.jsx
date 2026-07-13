import {
  Search, MapPin, Target, Code2, ShoppingCart, Stethoscope, Scale, LayoutGrid, Home,
  ChevronRight, ArrowUpRight, ArrowDown, ArrowRight, Star, Plus, Minus, Menu, X,
  MessageCircle, Phone, Check, TrendingUp, Zap, ShieldCheck, Users, Rocket, Mail,
  Clock, Send, Briefcase, Award, Calendar, Tag, User, BookOpen, Quote,
  ExternalLink, Filter, MapPinned, BarChart3,
  Building2, Globe, Sparkles, CheckCircle2,
} from "lucide-react";

// lucide-react dropped trademarked brand glyphs — hand-rolled outline equivalents, same 24x24/2px-stroke style.
function LinkedinGlyph({ size = 24, ...props }) {
  return (
    <svg viewBox="0 0 24 24" width={size} height={size} fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" {...props}>
      <path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-4 0v7h-4V8h4v1.5A5.98 5.98 0 0 1 16 8Z" />
      <rect x="2" y="9" width="4" height="12" />
      <circle cx="4" cy="4" r="2" />
    </svg>
  );
}
function InstagramGlyph({ size = 24, ...props }) {
  return (
    <svg viewBox="0 0 24 24" width={size} height={size} fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" {...props}>
      <rect x="2" y="2" width="20" height="20" rx="5" />
      <circle cx="12" cy="12" r="4" />
      <circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none" />
    </svg>
  );
}
function FacebookGlyph({ size = 24, ...props }) {
  return (
    <svg viewBox="0 0 24 24" width={size} height={size} fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" {...props}>
      <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3.5l.5-4H14V7a1 1 0 0 1 1-1h3Z" />
    </svg>
  );
}

const ICONS = {
  search: Search,
  "map-pin": MapPin,
  target: Target,
  "code-2": Code2,
  "shopping-cart": ShoppingCart,
  stethoscope: Stethoscope,
  scale: Scale,
  "layout-grid": LayoutGrid,
  home: Home,
  "chevron-right": ChevronRight,
  "arrow-up-right": ArrowUpRight,
  "arrow-down": ArrowDown,
  "arrow-right": ArrowRight,
  star: Star,
  plus: Plus,
  minus: Minus,
  menu: Menu,
  x: X,
  "message-circle": MessageCircle,
  phone: Phone,
  check: Check,
  "trending-up": TrendingUp,
  zap: Zap,
  "shield-check": ShieldCheck,
  users: Users,
  rocket: Rocket,
  mail: Mail,
  clock: Clock,
  send: Send,
  briefcase: Briefcase,
  award: Award,
  calendar: Calendar,
  tag: Tag,
  user: User,
  "book-open": BookOpen,
  quote: Quote,
  linkedin: LinkedinGlyph,
  instagram: InstagramGlyph,
  facebook: FacebookGlyph,
  "external-link": ExternalLink,
  filter: Filter,
  "map-pinned": MapPinned,
  "bar-chart-3": BarChart3,
  "building-2": Building2,
  globe: Globe,
  sparkles: Sparkles,
  "check-circle-2": CheckCircle2,
};

export default function Icon({ name, size = 20, style, className, ...rest }) {
  const Cmp = ICONS[name] || Sparkles;
  return <Cmp size={size} style={style} className={className} {...rest} />;
}
