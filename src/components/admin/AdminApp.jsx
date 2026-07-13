"use client";

import { useEffect, useState } from "react";
import Button from "@/components/ui/Button";
import Input from "@/components/ui/Input";
import Textarea from "@/components/ui/Textarea";
import Toast from "@/components/ui/Toast";
import { adminLogin, adminLogout, adminWhoAmI } from "@/lib/actions/adminAuth";
import {
  adminListPosts,
  adminSavePost,
  adminDeletePost,
  adminListCaseStudies,
  adminSaveCaseStudy,
  adminDeleteCaseStudy,
} from "@/lib/actions/admin";

const wrap = { maxWidth: "900px", margin: "0 auto", padding: "var(--space-2xl) var(--container-padding)", fontFamily: "var(--font-sans)" };
const cardStyle = { border: "1px solid var(--color-border)", borderRadius: "var(--radius-lg)", padding: "var(--space-lg)", marginBottom: "var(--space-md)" };
const rowStyle = { display: "flex", justifyContent: "space-between", alignItems: "flex-start", gap: "var(--space-md)" };
const tabStyle = (active) => ({
  padding: "10px 18px",
  borderRadius: "var(--radius-pill)",
  border: active ? "1px solid var(--color-accent)" : "1px solid var(--color-border)",
  background: active ? "var(--color-accent-tint-weak)" : "var(--color-bg)",
  color: active ? "var(--color-accent-dark)" : "var(--color-text-primary)",
  cursor: "pointer",
  font: "var(--font-label)",
});

const EMPTY_POST = { isNew: true, slug: "", title: "", excerpt: "", category: "", date: new Date().toISOString().slice(0, 10), readTime: "5 min read", author: "", content: "" };
const EMPTY_CASE = { isNew: true, slug: "", client: "", industry: "", location: "", title: "", summary: "", stats: "", challenge: "", approach: "", outcome: "" };

export default function AdminApp() {
  const [tokenInput, setTokenInput] = useState("");
  const [unlocked, setUnlocked] = useState(false);
  const [checkingSession, setCheckingSession] = useState(true);
  const [authError, setAuthError] = useState("");
  const [tab, setTab] = useState("posts");
  const [posts, setPosts] = useState([]);
  const [caseStudies, setCaseStudies] = useState([]);
  const [editingPost, setEditingPost] = useState(null);
  const [editingCase, setEditingCase] = useState(null);
  const [message, setMessage] = useState(null);

  useEffect(() => {
    adminWhoAmI().then((res) => {
      setCheckingSession(false);
      if (res.ok) {
        setUnlocked(true);
        refreshAll();
      }
    });
  }, []);

  async function tryUnlock(candidate) {
    const res = await adminLogin(candidate);
    if (res.ok) {
      setUnlocked(true);
      setAuthError("");
      refreshAll();
    } else {
      setAuthError(res.error);
      setUnlocked(false);
    }
  }

  async function refreshAll() {
    const [p, c] = await Promise.all([adminListPosts(), adminListCaseStudies()]);
    setPosts(p);
    setCaseStudies(c);
  }

  async function logout() {
    await adminLogout();
    setUnlocked(false);
    setTokenInput("");
  }

  async function savePost(form) {
    try {
      await adminSavePost(form);
      setMessage({ tone: "success", text: `Saved "${form.title}".` });
      setEditingPost(null);
      refreshAll();
    } catch (err) {
      setMessage({ tone: "danger", text: err.message });
    }
  }

  async function removePost(slug) {
    if (!confirm(`Delete post "${slug}"? This can't be undone.`)) return;
    try {
      await adminDeletePost(slug);
      setMessage({ tone: "success", text: "Post deleted." });
      refreshAll();
    } catch (err) {
      setMessage({ tone: "danger", text: err.message });
    }
  }

  async function saveCase(form) {
    try {
      await adminSaveCaseStudy(form);
      setMessage({ tone: "success", text: `Saved "${form.title}".` });
      setEditingCase(null);
      refreshAll();
    } catch (err) {
      setMessage({ tone: "danger", text: err.message });
    }
  }

  async function removeCase(slug) {
    if (!confirm(`Delete case study "${slug}"? This can't be undone.`)) return;
    try {
      await adminDeleteCaseStudy(slug);
      setMessage({ tone: "success", text: "Case study deleted." });
      refreshAll();
    } catch (err) {
      setMessage({ tone: "danger", text: err.message });
    }
  }

  if (checkingSession) {
    return <div style={wrap} />;
  }

  if (!unlocked) {
    return (
      <div style={wrap}>
        <h1 style={{ font: "var(--font-h1)", color: "var(--color-text-primary)", margin: "0 0 var(--space-md)" }}>Admin</h1>
        <p style={{ font: "var(--font-body)", color: "var(--color-text-secondary)", margin: "0 0 var(--space-md)" }}>
          Enter the admin token (set via the <code>ADMIN_TOKEN</code> environment variable) to manage blog posts and case studies.
        </p>
        <div style={{ display: "flex", gap: "var(--space-sm)", maxWidth: "420px" }}>
          <div style={{ flex: 1 }}>
            <Input label="Admin token" type="password" value={tokenInput} onChange={(e) => setTokenInput(e.target.value)} />
          </div>
          <div style={{ paddingTop: "26px" }}>
            <Button variant="primary" onClick={() => tryUnlock(tokenInput)}>
              Unlock
            </Button>
          </div>
        </div>
        {authError && (
          <div style={{ marginTop: "var(--space-md)", maxWidth: "420px" }}>
            <Toast message={authError} tone="danger" />
          </div>
        )}
      </div>
    );
  }

  return (
    <div style={wrap}>
      <div style={{ ...rowStyle, marginBottom: "var(--space-lg)" }}>
        <h1 style={{ font: "var(--font-h1)", color: "var(--color-text-primary)", margin: 0 }}>Admin</h1>
        <Button variant="secondary" onClick={logout}>
          Log out
        </Button>
      </div>

      {message && (
        <div style={{ marginBottom: "var(--space-md)" }}>
          <Toast message={message.text} tone={message.tone} onClose={() => setMessage(null)} />
        </div>
      )}

      <div style={{ display: "flex", gap: "var(--space-sm)", marginBottom: "var(--space-lg)" }}>
        <button style={tabStyle(tab === "posts")} onClick={() => setTab("posts")}>
          Blog Posts ({posts.length})
        </button>
        <button style={tabStyle(tab === "case-studies")} onClick={() => setTab("case-studies")}>
          Case Studies ({caseStudies.length})
        </button>
      </div>

      {tab === "posts" && (
        <PostsTab
          posts={posts}
          editing={editingPost}
          onEdit={setEditingPost}
          onSave={savePost}
          onDelete={removePost}
          onCancel={() => setEditingPost(null)}
        />
      )}

      {tab === "case-studies" && (
        <CaseStudiesTab
          caseStudies={caseStudies}
          editing={editingCase}
          onEdit={setEditingCase}
          onSave={saveCase}
          onDelete={removeCase}
          onCancel={() => setEditingCase(null)}
        />
      )}
    </div>
  );
}

function PostsTab({ posts, editing, onEdit, onSave, onDelete, onCancel }) {
  if (editing) {
    const initial = editing === "new" ? EMPTY_POST : { ...posts.find((p) => p.slug === editing), isNew: false, content: posts.find((p) => p.slug === editing).content.join("\n") };
    return <PostForm initial={initial} onSave={onSave} onCancel={onCancel} />;
  }
  return (
    <div>
      <Button variant="primary" onClick={() => onEdit("new")}>
        + New Post
      </Button>
      <div style={{ marginTop: "var(--space-md)" }}>
        {posts.map((p) => (
          <div key={p.slug} style={cardStyle}>
            <div style={rowStyle}>
              <div>
                <div style={{ font: "var(--font-h4)", color: "var(--color-text-primary)" }}>{p.title}</div>
                <div style={{ font: "var(--font-body-sm)", color: "var(--color-text-secondary)" }}>
                  /{p.slug} &middot; {p.category} &middot; {p.date}
                </div>
              </div>
              <div style={{ display: "flex", gap: "8px" }}>
                <Button variant="secondary" onClick={() => onEdit(p.slug)}>
                  Edit
                </Button>
                <Button variant="ghost" onClick={() => onDelete(p.slug)}>
                  Delete
                </Button>
              </div>
            </div>
          </div>
        ))}
      </div>
    </div>
  );
}

function PostForm({ initial, onSave, onCancel }) {
  const [form, setForm] = useState(initial);
  const set = (key) => (e) => setForm((f) => ({ ...f, [key]: e.target.value }));
  return (
    <div style={cardStyle}>
      <div style={{ display: "flex", flexDirection: "column", gap: "var(--space-sm)" }}>
        {form.isNew && <Input label="Slug (leave blank to generate from title)" value={form.slug} onChange={set("slug")} placeholder="my-post-slug" />}
        <Input label="Title" required value={form.title} onChange={set("title")} />
        <Input label="Excerpt" required value={form.excerpt} onChange={set("excerpt")} />
        <Input label="Category" required value={form.category} onChange={set("category")} placeholder="SEO" />
        <Input label="Date" type="date" required value={form.date} onChange={set("date")} />
        <Input label="Read time" required value={form.readTime} onChange={set("readTime")} placeholder="5 min read" />
        <Input label="Author" required value={form.author} onChange={set("author")} />
        <Textarea label="Content (one paragraph per line)" required rows={8} value={form.content} onChange={set("content")} />
        <div style={{ display: "flex", gap: "8px" }}>
          <Button variant="primary" onClick={() => onSave(form)}>
            Save
          </Button>
          <Button variant="secondary" onClick={onCancel}>
            Cancel
          </Button>
        </div>
      </div>
    </div>
  );
}

function CaseStudiesTab({ caseStudies, editing, onEdit, onSave, onDelete, onCancel }) {
  if (editing) {
    const found = editing === "new" ? null : caseStudies.find((c) => c.slug === editing);
    const initial =
      editing === "new"
        ? EMPTY_CASE
        : { ...found, isNew: false, stats: found.stats.map((s) => `${s.value} | ${s.label}`).join("\n") };
    return <CaseStudyForm initial={initial} onSave={onSave} onCancel={onCancel} />;
  }
  return (
    <div>
      <Button variant="primary" onClick={() => onEdit("new")}>
        + New Case Study
      </Button>
      <div style={{ marginTop: "var(--space-md)" }}>
        {caseStudies.map((c) => (
          <div key={c.slug} style={cardStyle}>
            <div style={rowStyle}>
              <div>
                <div style={{ font: "var(--font-h4)", color: "var(--color-text-primary)" }}>{c.title}</div>
                <div style={{ font: "var(--font-body-sm)", color: "var(--color-text-secondary)" }}>
                  /{c.slug} &middot; {c.client} &middot; {c.industry}
                </div>
              </div>
              <div style={{ display: "flex", gap: "8px" }}>
                <Button variant="secondary" onClick={() => onEdit(c.slug)}>
                  Edit
                </Button>
                <Button variant="ghost" onClick={() => onDelete(c.slug)}>
                  Delete
                </Button>
              </div>
            </div>
          </div>
        ))}
      </div>
    </div>
  );
}

function CaseStudyForm({ initial, onSave, onCancel }) {
  const [form, setForm] = useState(initial);
  const set = (key) => (e) => setForm((f) => ({ ...f, [key]: e.target.value }));
  return (
    <div style={cardStyle}>
      <div style={{ display: "flex", flexDirection: "column", gap: "var(--space-sm)" }}>
        {form.isNew && <Input label="Slug (leave blank to generate from title)" value={form.slug} onChange={set("slug")} placeholder="my-case-study-slug" />}
        <Input label="Title" required value={form.title} onChange={set("title")} />
        <Input label="Client" required value={form.client} onChange={set("client")} />
        <Input label="Industry" required value={form.industry} onChange={set("industry")} />
        <Input label="Location" required value={form.location} onChange={set("location")} placeholder="City, Country" />
        <Input label="Summary" required value={form.summary} onChange={set("summary")} />
        <Textarea label="Stats — one per line, format: value | label" required rows={3} value={form.stats} onChange={set("stats")} placeholder={"220% | Organic traffic growth"} />
        <Textarea label="Challenge" required rows={3} value={form.challenge} onChange={set("challenge")} />
        <Textarea label="Approach" required rows={3} value={form.approach} onChange={set("approach")} />
        <Textarea label="Outcome" required rows={3} value={form.outcome} onChange={set("outcome")} />
        <div style={{ display: "flex", gap: "8px" }}>
          <Button variant="primary" onClick={() => onSave(form)}>
            Save
          </Button>
          <Button variant="secondary" onClick={onCancel}>
            Cancel
          </Button>
        </div>
      </div>
    </div>
  );
}
