import { NextResponse } from "next/server";
import { updatePlan, getPlanBySlug } from "@/lib/repositories/pricing";

export async function PUT(request, { params }) {
  const { slug } = await params;
  const body = await request.json().catch(() => null);
  if (!body) {
    return NextResponse.json({ error: "Invalid request body." }, { status: 400 });
  }

  if (!getPlanBySlug(slug)) {
    return NextResponse.json({ error: "Plan not found." }, { status: 404 });
  }

  const plan = updatePlan(slug, body);
  return NextResponse.json({ plan });
}
