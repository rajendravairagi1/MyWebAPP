import { NextResponse } from "next/server";
import { listPosts, createPost } from "@/lib/repositories/posts";

export async function GET() {
  return NextResponse.json({ posts: listPosts() });
}

export async function POST(request) {
  const body = await request.json().catch(() => null);
  if (!body) {
    return NextResponse.json({ error: "Invalid request body." }, { status: 400 });
  }

  try {
    const post = createPost(body);
    return NextResponse.json({ post }, { status: 201 });
  } catch (error) {
    return NextResponse.json({ error: error.message }, { status: 400 });
  }
}
