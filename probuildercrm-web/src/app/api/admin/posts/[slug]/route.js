import { NextResponse } from "next/server";
import { getPostBySlug, updatePost, deletePost } from "@/lib/repositories/posts";

export async function GET(request, { params }) {
  const { slug } = await params;
  const post = getPostBySlug(slug);
  if (!post) {
    return NextResponse.json({ error: "Post not found." }, { status: 404 });
  }
  return NextResponse.json({ post });
}

export async function PUT(request, { params }) {
  const { slug } = await params;
  const body = await request.json().catch(() => null);
  if (!body) {
    return NextResponse.json({ error: "Invalid request body." }, { status: 400 });
  }

  try {
    const post = updatePost(slug, body);
    return NextResponse.json({ post });
  } catch (error) {
    return NextResponse.json({ error: error.message }, { status: 400 });
  }
}

export async function DELETE(request, { params }) {
  const { slug } = await params;
  deletePost(slug);
  return NextResponse.json({ ok: true });
}
