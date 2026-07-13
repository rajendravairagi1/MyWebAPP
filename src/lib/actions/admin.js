"use server";

import { revalidatePath } from "next/cache";
import * as posts from "@/lib/repositories/posts";
import * as caseStudies from "@/lib/repositories/caseStudies";

function assertAdmin(token) {
  const expected = process.env.ADMIN_TOKEN;
  if (!expected) {
    throw new Error("Admin editing is disabled on this deployment — set ADMIN_TOKEN in the environment to enable it.");
  }
  if (!token || token !== expected) {
    throw new Error("Invalid admin token.");
  }
}

export async function adminCheckToken(token) {
  try {
    assertAdmin(token);
    return { ok: true };
  } catch (err) {
    return { ok: false, error: err.message };
  }
}

export async function adminListPosts(token) {
  assertAdmin(token);
  return posts.listPosts();
}

export async function adminSavePost(token, { isNew, slug, title, excerpt, category, date, readTime, author, content }) {
  assertAdmin(token);
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

export async function adminDeletePost(token, slug) {
  assertAdmin(token);
  posts.deletePost(slug);
  revalidatePath("/blog");
  revalidatePath(`/blog/${slug}`);
  revalidatePath("/sitemap.xml");
}

export async function adminListCaseStudies(token) {
  assertAdmin(token);
  return caseStudies.listCaseStudies();
}

export async function adminSaveCaseStudy(token, { isNew, slug, client, industry, location, title, summary, stats, challenge, approach, outcome }) {
  assertAdmin(token);
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

export async function adminDeleteCaseStudy(token, slug) {
  assertAdmin(token);
  caseStudies.deleteCaseStudy(slug);
  revalidatePath("/portfolio");
  revalidatePath(`/portfolio/${slug}`);
  revalidatePath("/sitemap.xml");
}
