/** Apply saved text highlights to section HTML (ported from mobile SectionDetailScreen). */
export function applyTextHighlightsToHtml(html, highlights) {
  if (!html || !highlights?.length) return html;
  let content = html;
  highlights.forEach((h) => {
    const text = (h.text || '').trim();
    if (!text) return;
    const escaped = text.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    const regex = new RegExp(escaped, 'gi');
    content = content.replace(regex, (match) => `<mark class="reader-highlight">${match}</mark>`);
  });
  return content;
}
