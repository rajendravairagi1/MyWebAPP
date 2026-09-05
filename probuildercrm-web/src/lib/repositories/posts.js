import { getDb } from "@/lib/db";
import { slugify } from "@/lib/slugify";

function rowToPost(row) {
  if (!row) return null;
  return {
    slug: row.slug,
    title: row.title,
    excerpt: row.excerpt,
    category: row.category,
    date: row.date,
    readTime: row.read_time,
    author: row.author,
    content: JSON.parse(row.content),
    updatedAt: row.updated_at,
  };
}

export function listPosts() {
  const rows = getDb().prepare("SELECT * FROM blog_posts ORDER BY date DESC").all();
  return rows.map(rowToPost);
}

export function getPostBySlug(slug) {
  const row = getDb().prepare("SELECT * FROM blog_posts WHERE slug = ?").get(slug);
  return rowToPost(row);
}

export function createPost({ slug, title, excerpt, category, date, readTime, author, content }) {
  const finalSlug = slugify(slug || title);
  if (!finalSlug) throw new Error("A title or slug is required.");
  if (getPostBySlug(finalSlug)) throw new Error(`A post with slug "${finalSlug}" already exists.`);

  getDb()
    .prepare(
      `INSERT INTO blog_posts (slug, title, excerpt, category, date, read_time, author, content)
       VALUES (?, ?, ?, ?, ?, ?, ?, ?)`,
    )
    .run(finalSlug, title, excerpt, category, date, readTime, author, JSON.stringify(content));

  return getPostBySlug(finalSlug);
}

export function updatePost(originalSlug, { slug, title, excerpt, category, date, readTime, author, content }) {
  const finalSlug = slugify(slug || title);
  const existing = getPostBySlug(originalSlug);
  if (!existing) throw new Error(`No post found with slug "${originalSlug}".`);

  if (finalSlug !== originalSlug && getPostBySlug(finalSlug)) {
    throw new Error(`A post with slug "${finalSlug}" already exists.`);
  }

  getDb()
    .prepare(
      `UPDATE blog_posts
       SET slug = ?, title = ?, excerpt = ?, category = ?, date = ?, read_time = ?, author = ?, content = ?, updated_at = datetime('now')
       WHERE slug = ?`,
    )
    .run(finalSlug, title, excerpt, category, date, readTime, author, JSON.stringify(content), originalSlug);

  return getPostBySlug(finalSlug);
}

export function deletePost(slug) {
  getDb().prepare("DELETE FROM blog_posts WHERE slug = ?").run(slug);
}
