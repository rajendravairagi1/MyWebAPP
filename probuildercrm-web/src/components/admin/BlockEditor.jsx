"use client";

function emptyBlock(type) {
  if (type === "list") return { type: "list", items: [""] };
  return { type, text: "" };
}

export default function BlockEditor({ blocks, onChange }) {
  function updateBlock(index, next) {
    const copy = [...blocks];
    copy[index] = next;
    onChange(copy);
  }

  function removeBlock(index) {
    onChange(blocks.filter((_, i) => i !== index));
  }

  function moveBlock(index, direction) {
    const target = index + direction;
    if (target < 0 || target >= blocks.length) return;
    const copy = [...blocks];
    [copy[index], copy[target]] = [copy[target], copy[index]];
    onChange(copy);
  }

  function addBlock(type) {
    onChange([...blocks, emptyBlock(type)]);
  }

  return (
    <div style={{ display: "flex", flexDirection: "column", gap: 12 }}>
      {blocks.map((block, index) => (
        <div key={index} style={{ border: "1px solid var(--color-border)", borderRadius: "var(--radius-sm)", padding: 12 }}>
          <div style={{ display: "flex", justifyContent: "space-between", alignItems: "center", marginBottom: 8 }}>
            <span style={{ fontSize: "0.75rem", fontWeight: 700, textTransform: "uppercase", color: "var(--color-ink-soft)" }}>
              {block.type}
            </span>
            <div style={{ display: "flex", gap: 6 }}>
              <button type="button" onClick={() => moveBlock(index, -1)} style={miniBtn}>
                ↑
              </button>
              <button type="button" onClick={() => moveBlock(index, 1)} style={miniBtn}>
                ↓
              </button>
              <button type="button" onClick={() => removeBlock(index)} style={{ ...miniBtn, color: "#dc2626" }}>
                Remove
              </button>
            </div>
          </div>

          {block.type === "list" ? (
            <div style={{ display: "flex", flexDirection: "column", gap: 6 }}>
              {block.items.map((item, itemIndex) => (
                <div key={itemIndex} style={{ display: "flex", gap: 6 }}>
                  <input
                    value={item}
                    onChange={(e) => {
                      const items = [...block.items];
                      items[itemIndex] = e.target.value;
                      updateBlock(index, { ...block, items });
                    }}
                    style={inputStyle}
                  />
                  <button
                    type="button"
                    onClick={() => updateBlock(index, { ...block, items: block.items.filter((_, i) => i !== itemIndex) })}
                    style={miniBtn}
                  >
                    ×
                  </button>
                </div>
              ))}
              <button type="button" onClick={() => updateBlock(index, { ...block, items: [...block.items, ""] })} style={miniBtn}>
                + List item
              </button>
            </div>
          ) : (
            <textarea
              value={block.text}
              onChange={(e) => updateBlock(index, { ...block, text: e.target.value })}
              rows={block.type === "heading" ? 1 : 3}
              style={{ ...inputStyle, resize: "vertical" }}
            />
          )}
        </div>
      ))}

      <div style={{ display: "flex", gap: 8 }}>
        <button type="button" onClick={() => addBlock("paragraph")} className="btn btn-secondary">
          + Paragraph
        </button>
        <button type="button" onClick={() => addBlock("heading")} className="btn btn-secondary">
          + Heading
        </button>
        <button type="button" onClick={() => addBlock("list")} className="btn btn-secondary">
          + List
        </button>
      </div>
    </div>
  );
}

const inputStyle = {
  width: "100%",
  padding: "8px 10px",
  borderRadius: 6,
  border: "1px solid var(--color-border)",
  font: "var(--font-body)",
  fontSize: "0.92rem",
};

const miniBtn = {
  background: "none",
  border: "1px solid var(--color-border)",
  borderRadius: 6,
  padding: "4px 8px",
  fontSize: "0.8rem",
  cursor: "pointer",
};
