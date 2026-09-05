/** @type {import('next').NextConfig} */
const nextConfig = {
  turbopack: {
    root: __dirname,
  },
  // better-sqlite3 is a native addon (a compiled .node binary) — bundling
  // it like ordinary JS breaks the native binding, so it must be excluded
  // from the server bundle and loaded via a normal require() at runtime.
  serverExternalPackages: ["better-sqlite3"],
};

module.exports = nextConfig;
