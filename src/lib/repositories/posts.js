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

export function updatePost(slug, { title, excerpt, category, date, readTime, author, content }) {
  if (!getPostBySlug(slug)) throw new Error(`No post found with slug "${slug}".`);

  getDb()
    .prepare(
      `UPDATE blog_posts
       SET title = ?, excerpt = ?, category = ?, date = ?, read_time = ?, author = ?, content = ?, updated_at = datetime('now')
       WHERE slug = ?`,
    )
    .run(title, excerpt, category, date, readTime, author, JSON.stringify(content), slug);

  return getPostBySlug(slug);
}

export function deletePost(slug) {
  const result = getDb().prepare("DELETE FROM blog_posts WHERE slug = ?").run(slug);
  if (result.changes === 0) throw new Error(`No post found with slug "${slug}".`);
}
