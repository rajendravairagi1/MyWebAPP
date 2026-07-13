import { getDb } from "@/lib/db";
import { slugify } from "@/lib/slugify";

function rowToCaseStudy(row) {
  if (!row) return null;
  return {
    slug: row.slug,
    client: row.client,
    industry: row.industry,
    location: row.location,
    title: row.title,
    summary: row.summary,
    stats: JSON.parse(row.stats),
    challenge: row.challenge,
    approach: row.approach,
    outcome: row.outcome,
    updatedAt: row.updated_at,
  };
}

export function listCaseStudies() {
  const rows = getDb().prepare("SELECT * FROM case_studies ORDER BY created_at DESC").all();
  return rows.map(rowToCaseStudy);
}

export function getCaseStudyBySlug(slug) {
  const row = getDb().prepare("SELECT * FROM case_studies WHERE slug = ?").get(slug);
  return rowToCaseStudy(row);
}

export function createCaseStudy({ slug, client, industry, location, title, summary, stats, challenge, approach, outcome }) {
  const finalSlug = slugify(slug || title);
  if (!finalSlug) throw new Error("A title or slug is required.");
  if (getCaseStudyBySlug(finalSlug)) throw new Error(`A case study with slug "${finalSlug}" already exists.`);

  getDb()
    .prepare(
      `INSERT INTO case_studies (slug, client, industry, location, title, summary, stats, challenge, approach, outcome)
       VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)`,
    )
    .run(finalSlug, client, industry, location, title, summary, JSON.stringify(stats), challenge, approach, outcome);

  return getCaseStudyBySlug(finalSlug);
}

export function updateCaseStudy(slug, { client, industry, location, title, summary, stats, challenge, approach, outcome }) {
  if (!getCaseStudyBySlug(slug)) throw new Error(`No case study found with slug "${slug}".`);

  getDb()
    .prepare(
      `UPDATE case_studies
       SET client = ?, industry = ?, location = ?, title = ?, summary = ?, stats = ?, challenge = ?, approach = ?, outcome = ?, updated_at = datetime('now')
       WHERE slug = ?`,
    )
    .run(client, industry, location, title, summary, JSON.stringify(stats), challenge, approach, outcome, slug);

  return getCaseStudyBySlug(slug);
}

export function deleteCaseStudy(slug) {
  const result = getDb().prepare("DELETE FROM case_studies WHERE slug = ?").run(slug);
  if (result.changes === 0) throw new Error(`No case study found with slug "${slug}".`);
}
