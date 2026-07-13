"use client";

import Link from "next/link";

const base = {
  display: "inline-flex",
  alignItems: "center",
  justifyContent: "center",
  gap: "8px",
  fontFamily: "var(--font-sans)",
  fontWeight: "var(--weight-semibold)",
  letterSpacing: "var(--tracking-snug)",
  borderRadius: "var(--radius-sm)",
  border: "1px solid transparent",
  minHeight: "var(--min-tap)",
  textDecoration: "none",
  boxSizing: "border-box",
  whiteSpace: "nowrap",
  flexShrink: 0,
  transition:
    "background var(--transition-fast), border-color var(--transition-fast), color var(--transition-fast)",
};

const variants = {
  primary: {
    background: "var(--color-accent)",
    color: "var(--color-text-on-accent)",
    borderColor: "var(--color-accent)",
  },
  secondary: {
    background: "var(--color-bg)",
    color: "var(--color-text-primary)",
    borderColor: "var(--color-border)",
  },
  ghost: {
    background: "transparent",
    color: "var(--color-accent-dark)",
    borderColor: "transparent",
  },
};

export default function Button({
  children,
  variant = "primary",
  size = "md",
  disabled = false,
  href,
  onClick,
  type = "button",
  style: styleOverride,
  ...rest
}) {
  const style = {
    ...base,
    ...variants[variant],
    fontSize: size === "lg" ? "var(--text-lg)" : "var(--text-base)",
    padding:
      variant === "ghost"
        ? size === "lg"
          ? "14px 8px"
          : "12px 8px"
        : size === "lg"
          ? "14px 28px"
          : "12px 22px",
    cursor: disabled ? "not-allowed" : "pointer",
    opacity: disabled ? 0.5 : 1,
    ...styleOverride,
  };

  const handleEnter = (e) => {
    if (disabled) return;
    if (variant === "primary") e.currentTarget.style.background = "var(--color-accent-dark)";
    if (variant === "secondary") e.currentTarget.style.borderColor = "var(--color-border-strong)";
    if (variant === "ghost") e.currentTarget.style.background = "var(--color-accent-tint-weak)";
  };
  const handleLeave = (e) => {
    Object.assign(e.currentTarget.style, variants[variant]);
  };

  if (href) {
    const isInternal = href.startsWith("/") || href.startsWith("#");
    if (isInternal && !href.startsWith("#")) {
      return (
        <Link href={href} style={style} onMouseEnter={handleEnter} onMouseLeave={handleLeave} {...rest}>
          {children}
        </Link>
      );
    }
    return (
      <a
        href={href}
        style={style}
        onMouseEnter={handleEnter}
        onMouseLeave={handleLeave}
        target={href.startsWith("http") ? "_blank" : undefined}
        rel={href.startsWith("http") ? "noopener noreferrer" : undefined}
        {...rest}
      >
        {children}
      </a>
    );
  }

  return (
    <button
      type={type}
      disabled={disabled}
      onClick={disabled ? undefined : onClick}
      style={style}
      onMouseEnter={handleEnter}
      onMouseLeave={handleLeave}
      {...rest}
    >
      {children}
    </button>
  );
}
