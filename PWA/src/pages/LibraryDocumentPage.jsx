import { useEffect, useState } from 'react';
import { useParams } from 'react-router-dom';
import { getLibraryDocument } from '../api/libraryApi';
import { catchMessage } from '../lib/apiErrors';

function stripHtml(html) {
  if (!html || typeof html !== 'string') return '';
  const div = document.createElement('div');
  div.innerHTML = html;
  return div.textContent || div.innerText || '';
}

export default function LibraryDocumentPage() {
  const { documentId } = useParams();
  const [doc, setDoc] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    getLibraryDocument(documentId)
      .then(setDoc)
      .catch((e) => setError(catchMessage(e, 'Failed to load document.')))
      .finally(() => setLoading(false));
  }, [documentId]);

  if (loading) return <p className="p-4 text-sm text-app-subtle">Loading…</p>;
  if (error) return <p className="p-4 text-sm text-app-error">{error}</p>;

  const body = doc?.body_html ? stripHtml(doc.body_html) : doc?.body || '';

  return (
    <article className="p-4 pb-8">
      <h2 className="mb-4 text-xl font-bold">{doc?.title}</h2>
      {doc?.summary ? <p className="mb-4 text-sm text-app-subtle">{doc.summary}</p> : null}
      {body ? (
        <p className="whitespace-pre-wrap text-sm leading-relaxed">{body}</p>
      ) : (
        <p className="text-sm text-app-muted">No content available.</p>
      )}
    </article>
  );
}
