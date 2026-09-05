"use client";

import { useEffect, useState } from "react";
import { useRouter } from "next/navigation";
import BlockEditor from "./BlockEditor";

function emptyForm() {
  return {
    slug: "",
    title: "",
    excerpt: "",
    category: "",
    author: "",
    date: new Date().toISOString().slice(0, 10),
    readTime: "5 min read",
    content: [{ type: "paragraph", text: "" }],
  };
}

export default function AdminApp() {
  const router = useRouter();
  const [posts, setPosts] = useState([]);
  const [loading, setLoading] = useState(true);
  const [view, setView] = useState("list"); // list | edit
  const [editingSlug, setEditingSlug] = useState(null);
  const [form, setForm] = useState(emptyForm());
  const [saving, setSaving] = useState(false);
  const [error, setError] = useState("");

  async function loadPosts() {
    setLoading(true);
    const response = await fetch("/api/admin/posts");
    const body = await response.json();
    setPosts(body.posts || []);
    setLoading(false);
  }

  useEffect(() => {
    loadPosts();
  }, []);

  function startNew() {
    setForm(emptyForm());
    setEditingSlug(null);
    setError("");
    setView("edit");
  }

  function startEdit(post) {
    setForm({
      slug: post.slug,
      title: post.title,
      excerpt: post.excerpt,
      category: post.category,
      author: post.author,
      date: post.date,
      readTime: post.readTime,
      content: post.content,
    });
    setEditingSlug(post.slug);
    setError("");
    setView("edit");
  }

  async function handleDelete(slug) {
    if (!window.confirm("Delete this post? This cannot be undone.")) return;
    await fetch(`/api/admin/posts/${slug}`, { method: "DELETE" });
    loadPosts();
  }

  async function handleSave(event) {
    event.preventDefault();
    setSaving(true);
    setError("");

    try {
      const isEdit = Boolean(editingSlug);
      const response = await fetch(isEdit ? `/api/admin/posts/${editingSlug}` : "/api/admin/posts", {
        method: isEdit ? "PUT" : "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(form),
      });

      const body = await response.json();
      if (!response.ok) throw new Error(body.error || "Failed to save post.");

      await loadPosts();
      setView("list");
    } catch (err) {
      setError(err.message);
    } finally {
      setSaving(false);
    }
  }

  async function handleLogout() {
    await fetch("/api/admin/logout", { method: "POST" });
    router.push("/admin/login");
    router.refresh();
  }

  return (
    <div style={{ minHeight: "70vh", background: "var(--color-bg-soft)", padding: "var(--space-xl) 0" }}>
      <div className="container" style={{ maxWidth: 900 }}>
        <div style={{ display: "flex", justifyContent: "space-between", alignItems: "center", marginBottom: "var(--space-lg)" }}>
          <h1 style={{ font: "var(--font-h2)", fontSize: "1.6rem", margin: 0 }}>Blog Admin</h1>
          <div style={{ display: "flex", gap: 10 }}>
            {view === "list" ? (
              <button type="button" onClick={startNew} className="btn btn-primary">
                + New Post
              </button>
            ) : (
              <button type="button" onClick={() => setView("list")} className="btn btn-secondary">
                ← Back to list
              </button>
            )}
            <button type="button" onClick={handleLogout} className="btn btn-secondary">
              Log Out
            </button>
          </div>
        </div>

        {view === "list" && (
          <div className="card" style={{ padding: 0, overflow: "hidden" }}>
            {loading ? (
              <p style={{ padding: 20, margin: 0 }}>Loading…</p>
            ) : posts.length === 0 ? (
              <p style={{ padding: 20, margin: 0, color: "var(--color-ink-soft)" }}>No posts yet — click “+ New Post” to add one.</p>
            ) : (
              <table style={{ width: "100%", borderCollapse: "collapse" }}>
                <tbody>
                  {posts.map((post) => (
                    <tr key={post.slug} style={{ borderBottom: "1px solid var(--color-border)" }}>
                      <td style={{ padding: "14px 16px" }}>
                        <div style={{ fontWeight: 600 }}>{post.title}</div>
                        <div style={{ fontSize: "0.85rem", color: "var(--color-ink-soft)" }}>
                          {post.category} · {post.date}
                        </div>
                      </td>
                      <td style={{ padding: "14px 16px", textAlign: "right", whiteSpace: "nowrap" }}>
                        <button type="button" onClick={() => startEdit(post)} className="btn btn-secondary" style={{ padding: "6px 14px", marginRight: 8 }}>
                          Edit
                        </button>
                        <button
                          type="button"
                          onClick={() => handleDelete(post.slug)}
                          className="btn btn-secondary"
                          style={{ padding: "6px 14px", color: "#dc2626", borderColor: "#fecaca" }}
                        >
                          Delete
                        </button>
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            )}
          </div>
        )}

        {view === "edit" && (
          <form onSubmit={handleSave} className="card" style={{ display: "flex", flexDirection: "column", gap: 16 }}>
            <Field label="Title" htmlFor="post-title">
              <input id="post-title" required value={form.title} onChange={(e) => setForm({ ...form, title: e.target.value })} style={inputStyle} />
            </Field>

            <div style={{ display: "grid", gridTemplateColumns: "1fr 1fr", gap: 16 }}>
              <Field label="Category" htmlFor="post-category">
                <input id="post-category" required value={form.category} onChange={(e) => setForm({ ...form, category: e.target.value })} style={inputStyle} />
              </Field>
              <Field label="Author" htmlFor="post-author">
                <input id="post-author" required value={form.author} onChange={(e) => setForm({ ...form, author: e.target.value })} style={inputStyle} />
              </Field>
              <Field label="Date" htmlFor="post-date">
                <input id="post-date" type="date" required value={form.date} onChange={(e) => setForm({ ...form, date: e.target.value })} style={inputStyle} />
              </Field>
              <Field label="Read time" htmlFor="post-readtime">
                <input id="post-readtime" required value={form.readTime} onChange={(e) => setForm({ ...form, readTime: e.target.value })} style={inputStyle} />
              </Field>
            </div>

            <Field label="Excerpt (shown on the blog list page)" htmlFor="post-excerpt">
              <textarea id="post-excerpt" required rows={2} value={form.excerpt} onChange={(e) => setForm({ ...form, excerpt: e.target.value })} style={{ ...inputStyle, resize: "vertical" }} />
            </Field>

            <Field label="Content">
              <BlockEditor blocks={form.content} onChange={(content) => setForm({ ...form, content })} />
            </Field>

            {error && <p style={{ color: "#dc2626", margin: 0 }}>{error}</p>}

            <div>
              <button type="submit" className="btn btn-primary" disabled={saving}>
                {saving ? "Saving…" : "Save Post"}
              </button>
            </div>
          </form>
        )}
      </div>
    </div>
  );
}

function Field({ label, htmlFor, children }) {
  return (
    <div>
      <label htmlFor={htmlFor} style={{ display: "block", fontWeight: 600, marginBottom: 6, fontSize: "0.9rem" }}>
        {label}
      </label>
      {children}
    </div>
  );
}

const inputStyle = {
  width: "100%",
  padding: "9px 12px",
  borderRadius: "var(--radius-sm)",
  border: "1px solid var(--color-border)",
  font: "var(--font-body)",
};
