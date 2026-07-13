"use server";

import { revalidatePath } from "next/cache";
import { requireAdmin } from "@/lib/actions/adminAuth";
import * as posts from "@/lib/repositories/posts";
import * as caseStudies from "@/lib/repositories/caseStudies";

export async function adminListPosts() {
  await requireAdmin();
  return posts.listPosts();
}

export async function adminSavePost({ isNew, slug, title, excerpt, category, date, readTime, author, content }) {
  await requireAdmin();
  const contentParagraphs = content
    .split("\n")
    .map((p) => p.trim())
    .filter(Boolean);
  const payload = { slug, title, excerpt, category, date, readTime, author, content: contentParagraphs };

  const saved = isNew ? posts.createPost(payload) : posts.updatePost(slug, payload);

  revalidatePath("/blog");
  revalidatePath(`/blog/${saved.slug}`);
  revalidatePath("/sitemap.xml");
  return saved;
}

export async function adminDeletePost(slug) {
  await requireAdmin();
  posts.deletePost(slug);
  revalidatePath("/blog");
  revalidatePath(`/blog/${slug}`);
  revalidatePath("/sitemap.xml");
}

export async function adminListCaseStudies() {
  await requireAdmin();
  return caseStudies.listCaseStudies();
}

export async function adminSaveCaseStudy({ isNew, slug, client, industry, location, title, summary, stats, challenge, approach, outcome }) {
  await requireAdmin();
  const statList = stats
    .split("\n")
    .map((line) => {
      const [value, ...labelParts] = line.split("|");
      return { value: (value || "").trim(), label: labelParts.join("|").trim() };
    })
    .filter((s) => s.value && s.label);
  const payload = { slug, client, industry, location, title, summary, stats: statList, challenge, approach, outcome };

  const saved = isNew ? caseStudies.createCaseStudy(payload) : caseStudies.updateCaseStudy(slug, payload);

  revalidatePath("/portfolio");
  revalidatePath(`/portfolio/${saved.slug}`);
  revalidatePath("/sitemap.xml");
  return saved;
}

export async function adminDeleteCaseStudy(slug) {
  await requireAdmin();
  caseStudies.deleteCaseStudy(slug);
  revalidatePath("/portfolio");
  revalidatePath(`/portfolio/${slug}`);
  revalidatePath("/sitemap.xml");
}
