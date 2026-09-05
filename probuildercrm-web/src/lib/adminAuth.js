const COOKIE_NAME = "pb_admin_session";
const SESSION_MAX_AGE_SECONDS = 60 * 60 * 24 * 7; // 7 days

function getSecret() {
  const secret = process.env.ADMIN_PASSWORD;
  if (!secret) {
    throw new Error("ADMIN_PASSWORD is not set in the environment.");
  }
  return secret;
}

// Uses the Web Crypto API (globalThis.crypto.subtle) rather than Node's
// `crypto` module — this file is imported from middleware, which runs in
// the Edge Runtime and doesn't support Node-specific built-ins. Web Crypto
// works in both the Edge Runtime and modern Node, so the same code runs
// everywhere without a runtime-specific branch.
async function sign(value) {
  const key = await crypto.subtle.importKey("raw", new TextEncoder().encode(getSecret()), { name: "HMAC", hash: "SHA-256" }, false, [
    "sign",
  ]);
  const signature = await crypto.subtle.sign("HMAC", key, new TextEncoder().encode(value));
  return Array.from(new Uint8Array(signature))
    .map((b) => b.toString(16).padStart(2, "0"))
    .join("");
}

export function checkPassword(password) {
  return typeof password === "string" && password.length > 0 && password === getSecret();
}

export async function createSessionToken() {
  const issuedAt = Date.now().toString();
  return `${issuedAt}.${await sign(issuedAt)}`;
}

export async function isValidSessionToken(token) {
  if (!token || typeof token !== "string" || !token.includes(".")) return false;
  const [issuedAt, signature] = token.split(".");
  if (!issuedAt || !signature) return false;
  if ((await sign(issuedAt)) !== signature) return false;

  const ageSeconds = (Date.now() - Number(issuedAt)) / 1000;
  return ageSeconds >= 0 && ageSeconds <= SESSION_MAX_AGE_SECONDS;
}

export { COOKIE_NAME, SESSION_MAX_AGE_SECONDS };
