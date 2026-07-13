"use server";

import { cookies } from "next/headers";

const COOKIE_NAME = "admin_session";
const MAX_AGE_SECONDS = 60 * 60 * 8; // 8 hours

function cookieOptions() {
  return {
    httpOnly: true,
    secure: process.env.NODE_ENV === "production",
    sameSite: "lax",
    path: "/",
    maxAge: MAX_AGE_SECONDS,
  };
}

export async function requireAdmin() {
  const expected = process.env.ADMIN_TOKEN;
  if (!expected) {
    throw new Error("Admin editing is disabled on this deployment — set ADMIN_TOKEN in the environment to enable it.");
  }
  const store = await cookies();
  const session = store.get(COOKIE_NAME)?.value;
  if (!session || session !== expected) {
    throw new Error("Not signed in.");
  }
}

export async function adminLogin(token) {
  const expected = process.env.ADMIN_TOKEN;
  if (!expected) {
    return { ok: false, error: "Admin editing is disabled on this deployment — set ADMIN_TOKEN in the environment to enable it." };
  }
  if (!token || token !== expected) {
    return { ok: false, error: "Invalid admin token." };
  }
  const store = await cookies();
  store.set(COOKIE_NAME, token, cookieOptions());
  return { ok: true };
}

export async function adminLogout() {
  const store = await cookies();
  store.delete(COOKIE_NAME);
}

export async function adminWhoAmI() {
  try {
    await requireAdmin();
    return { ok: true };
  } catch (err) {
    return { ok: false, error: err.message };
  }
}
