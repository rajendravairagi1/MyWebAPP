import Database from "better-sqlite3";
import fs from "node:fs";
import path from "node:path";
import { BLOG_POSTS } from "@/data/blog";
import { CASE_STUDIES } from "@/data/portfolio";

const DB_PATH = path.join(process.cwd(), "data", "app.db");

let db;

function seedIfEmpty(database) {
  const postCount = database.prepare("SELECT COUNT(*) AS n FROM blog_posts").get().n;
  if (postCount === 0) {
    const insert = database.prepare(`
      INSERT INTO blog_posts (slug, title, excerpt, category, date, read_time, author, content)
      VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    `);
    for (const p of BLOG_POSTS) {
      insert.run(p.slug, p.title, p.excerpt, p.category, p.date, p.readTime, p.author, JSON.stringify(p.content));
    }
  }

  const caseCount = database.prepare("SELECT COUNT(*) AS n FROM case_studies").get().n;
  if (caseCount === 0) {
    const insert = database.prepare(`
      INSERT INTO case_studies (slug, client, industry, location, title, summary, stats, challenge, approach, outcome)
      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    `);
    for (const c of CASE_STUDIES) {
      insert.run(c.slug, c.client, c.industry, c.location, c.title, c.summary, JSON.stringify(c.stats), c.challenge, c.approach, c.outcome);
    }
  }
}

export function getDb() {
  if (db) return db;

  fs.mkdirSync(path.dirname(DB_PATH), { recursive: true });
  db = new Database(DB_PATH);
  db.pragma("journal_mode = WAL");

  db.exec(`
    CREATE TABLE IF NOT EXISTS blog_posts (
      slug TEXT PRIMARY KEY,
      title TEXT NOT NULL,
      excerpt TEXT NOT NULL,
      category TEXT NOT NULL,
      date TEXT NOT NULL,
      read_time TEXT NOT NULL,
      author TEXT NOT NULL,
      content TEXT NOT NULL,
      created_at TEXT NOT NULL DEFAULT (datetime('now')),
      updated_at TEXT NOT NULL DEFAULT (datetime('now'))
    );
    CREATE TABLE IF NOT EXISTS case_studies (
      slug TEXT PRIMARY KEY,
      client TEXT NOT NULL,
      industry TEXT NOT NULL,
      location TEXT NOT NULL,
      title TEXT NOT NULL,
      summary TEXT NOT NULL,
      stats TEXT NOT NULL,
      challenge TEXT NOT NULL,
      approach TEXT NOT NULL,
      outcome TEXT NOT NULL,
      created_at TEXT NOT NULL DEFAULT (datetime('now')),
      updated_at TEXT NOT NULL DEFAULT (datetime('now'))
    );
  `);

  seedIfEmpty(db);

  return db;
}
