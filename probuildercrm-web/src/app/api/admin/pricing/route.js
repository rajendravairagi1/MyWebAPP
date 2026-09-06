import { NextResponse } from "next/server";
import { listPlans } from "@/lib/repositories/pricing";

export async function GET() {
  return NextResponse.json({ plans: listPlans() });
}
