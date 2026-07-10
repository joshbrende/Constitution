import { useEffect, useState } from 'react';
import { useLocation, useParams } from 'react-router-dom';
import { getStaticPage } from '../api/staticPagesApi';
import { catchMessage } from '../lib/apiErrors';

export default function StaticPage() {
  const { slug } = useParams();
  const { state } = useLocation();
  const [page, setPage] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    getStaticPage(slug)
      .then(setPage)
      .catch((e) => setError(catchMessage(e, 'Failed to load page.')))
      .finally(() => setLoading(false));
  }, [slug]);

  if (loading) return <p className="p-4 text-sm text-app-subtle">Loading…</p>;
  if (error) return <p className="p-4 text-sm text-app-error">{error}</p>;

  const title = page?.title ?? state?.title ?? slug;

  return (
    <article className="p-4">
      <h1 className="mb-4 text-xl font-bold">{title}</h1>
      {page?.body ? (
        <div className="reader-body text-sm" dangerouslySetInnerHTML={{ __html: page.body }} />
      ) : (
        <p className="text-sm text-app-subtle">No content available.</p>
      )}
    </article>
  );
}
