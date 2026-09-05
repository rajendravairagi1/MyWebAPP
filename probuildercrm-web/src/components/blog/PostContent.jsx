export default function PostContent({ blocks }) {
  return (
    <div style={{ display: "flex", flexDirection: "column", gap: "var(--space-md)" }}>
      {blocks.map((block, index) => {
        if (block.type === "heading") {
          return (
            <h2 key={index} style={{ font: "var(--font-h2)", fontSize: "1.5rem", margin: "var(--space-sm) 0 0" }}>
              {block.text}
            </h2>
          );
        }
        if (block.type === "list") {
          return (
            <ul key={index} style={{ margin: 0, paddingLeft: "1.2rem", display: "flex", flexDirection: "column", gap: 6 }}>
              {block.items.map((item, itemIndex) => (
                <li key={itemIndex} style={{ color: "var(--color-ink-soft)", fontSize: "1.02rem", lineHeight: 1.7 }}>
                  {item}
                </li>
              ))}
            </ul>
          );
        }
        return (
          <p key={index} style={{ margin: 0, color: "var(--color-ink-soft)", fontSize: "1.05rem", lineHeight: 1.75 }}>
            {block.text}
          </p>
        );
      })}
    </div>
  );
}
