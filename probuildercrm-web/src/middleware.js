import { NextResponse } from "next/server";
import { COOKIE_NAME, isValidSessionToken } from "@/lib/adminAuth";

export async function middleware(request) {
  const { pathname } = request.nextUrl;
  const isApi = pathname.startsWith("/api/admin/posts");
  const isAdminPage = pathname.startsWith("/admin") && pathname !== "/admin/login";

  if (!isApi && !isAdminPage) {
    return NextResponse.next();
  }

  const token = request.cookies.get(COOKIE_NAME)?.value;
  const authed = await isValidSessionToken(token);

  if (authed) {
    return NextResponse.next();
  }

  if (isApi) {
    return NextResponse.json({ error: "Unauthorized" }, { status: 401 });
  }

  const loginUrl = new URL("/admin/login", request.url);
  return NextResponse.redirect(loginUrl);
}

export const config = {
  matcher: ["/admin/:path*", "/api/admin/posts/:path*"],
};
