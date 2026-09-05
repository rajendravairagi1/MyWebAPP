/**
 * Renders one or more Schema.org JSON-LD blocks. This is what search
 * engines (rich results) and AI answer engines (AEO/GEO — Google AI
 * Overviews, ChatGPT, Perplexity, etc.) use to understand and directly
 * cite a page's content, beyond what plain text alone conveys.
 */
export default function JsonLd({ data }) {
  const items = Array.isArray(data) ? data : [data];
  return (
    <>
      {items.map((item, index) => (
        <script
          key={index}
          type="application/ld+json"
          // eslint-disable-next-line react/no-danger
          dangerouslySetInnerHTML={{ __html: JSON.stringify(item) }}
        />
      ))}
    </>
  );
}
