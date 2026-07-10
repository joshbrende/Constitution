import { useCallback, useEffect, useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { getLibraryCategories, getLibraryDocuments } from '../api/libraryApi';
import { catchMessage } from '../lib/apiErrors';
import ScaleButton from '../components/ScaleButton';
import WorkflowIcon from '../ui/icons/WorkflowIcon';
import { WORKFLOW_ICON_KEYS } from '../ui/icons/workflowIcons';

export default function LibraryPage() {
  const navigate = useNavigate();
  const [categories, setCategories] = useState([]);
  const [documents, setDocuments] = useState([]);
  const [selectedCategoryId, setSelectedCategoryId] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  const load = useCallback(async () => {
    setLoading(true);
    setError(null);
    try {
      const [catsRes, docsRes] = await Promise.all([
        getLibraryCategories(),
        getLibraryDocuments({ category_id: selectedCategoryId || undefined, per_page: 30 }),
      ]);
      setCategories(Array.isArray(catsRes) ? catsRes : []);
      setDocuments(docsRes?.data ?? []);
    } catch (e) {
      setError(catchMessage(e, 'Failed to load library.'));
      setDocuments([]);
    } finally {
      setLoading(false);
    }
  }, [selectedCategoryId]);

  useEffect(() => {
    load();
  }, [load]);

  return (
    <div className="space-y-4 p-4 pb-8">
      <div>
        <h2 className="text-xl font-bold">Digital Library</h2>
        <p className="text-sm text-app-subtle">Party documents, policy papers, speeches, and resolutions.</p>
      </div>

      {error ? (
        <div className="rounded-lg border border-red-900/50 bg-red-950/20 p-3 text-sm text-app-error">
          {error}
          <button type="button" onClick={load} className="ml-2 text-app-gold">
            Retry
          </button>
        </div>
      ) : null}

      {categories.length > 0 && (
        <div className="flex gap-2 overflow-x-auto pb-1">
          <button
            type="button"
            onClick={() => setSelectedCategoryId(null)}
            className={`shrink-0 rounded-full px-3 py-1 text-xs ${
              !selectedCategoryId ? 'bg-app-green text-white' : 'bg-app-surface text-app-subtle'
            }`}
          >
            All
          </button>
          {categories.map((cat) => (
            <button
              key={cat.id}
              type="button"
              onClick={() => setSelectedCategoryId(selectedCategoryId === cat.id ? null : cat.id)}
              className={`shrink-0 rounded-full px-3 py-1 text-xs ${
                selectedCategoryId === cat.id ? 'bg-app-green text-white' : 'bg-app-surface text-app-subtle'
              }`}
            >
              {cat.name}
              {cat.documents_count > 0 ? ` (${cat.documents_count})` : ''}
            </button>
          ))}
        </div>
      )}

      {loading ? (
        <p className="text-sm text-app-subtle">Loading…</p>
      ) : documents.length === 0 ? (
        <div className="rounded-xl bg-app-surface p-8 text-center">
          <WorkflowIcon iconKey={WORKFLOW_ICON_KEYS.libraryDocument} variant="muted" size={40} className="mx-auto" />
          <p className="mt-2 text-sm text-app-muted">No documents in this category.</p>
        </div>
      ) : (
        documents.map((doc) => (
          <ScaleButton
            key={doc.id}
            onClick={() => navigate(`/home/library/${doc.id}`, { state: { title: doc.title } })}
            className="block w-full rounded-xl border border-app-border bg-app-surface p-3 text-left"
          >
            <p className="font-semibold">{doc.title}</p>
            {doc.summary ? <p className="line-clamp-2 text-xs text-app-subtle">{doc.summary}</p> : null}
          </ScaleButton>
        ))
      )}
    </div>
  );
}
