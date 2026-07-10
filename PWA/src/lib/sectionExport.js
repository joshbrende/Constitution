function escapeHtml(str) {
  if (str == null || typeof str !== 'string') return '';
  return str
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#039;');
}

/** Strip HTML tags for plain-text export fallback. */
function stripHtml(html) {
  if (!html) return '';
  const el = document.createElement('div');
  el.innerHTML = html;
  return el.textContent || '';
}

export function buildSectionExportHtml({
  docTitle,
  breadcrumb,
  logicalNumber,
  title,
  bodyHtml,
  bodyText,
}) {
  const plain = bodyText || stripHtml(bodyHtml);
  const paragraphs = plain
    .split(/\n\n+/)
    .filter((p) => p.trim())
    .map((p) => `<p>${escapeHtml(p.trim())}</p>`)
    .join('\n');
  const bodyContent =
    paragraphs ||
    (bodyHtml ? `<div class="body-html">${bodyHtml}</div>` : '<p><em>No content.</em></p>');

  return `<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8" />
    <title>${escapeHtml(logicalNumber || '')} ${escapeHtml(title || 'Section')}</title>
    <style>
      body { font-family: Georgia, serif; font-size: 12pt; line-height: 1.6; color: #000; padding: 24px; max-width: 640px; margin: 0 auto; }
      .doc-title { font-size: 10pt; color: #666; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 12px; }
      .meta { color: #666; font-size: 11pt; margin-bottom: 16px; }
      h1 { font-size: 14pt; margin-bottom: 16px; }
      p { margin: 0 0 1em 0; }
      .body-html p { margin: 0 0 1em 0; }
      @media print { body { padding: 0; } }
    </style>
  </head>
  <body>
    <div class="doc-title">${escapeHtml(docTitle || '')}</div>
    <div class="meta">${escapeHtml(breadcrumb || '')}</div>
    <h1>${escapeHtml(logicalNumber || '')} ${escapeHtml(title || '')}</h1>
    ${bodyContent}
  </body>
</html>`;
}

/** Opens a print dialog so the user can save as PDF (web equivalent of mobile export). */
export function exportSectionToPdf(options) {
  const html = buildSectionExportHtml(options);
  const win = window.open('', '_blank', 'noopener,noreferrer');
  if (!win) {
    throw new Error('Pop-up blocked. Allow pop-ups to export PDF.');
  }
  win.document.write(html);
  win.document.close();
  win.focus();
  setTimeout(() => {
    win.print();
  }, 300);
}
