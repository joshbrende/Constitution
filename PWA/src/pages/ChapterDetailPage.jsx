import { useEffect, useState } from 'react';
import { Link, useParams, useSearchParams } from 'react-router-dom';
import { catchMessage } from '../lib/apiErrors';
import { useReaderData } from '../context/ReaderDataContext';
import { loadChapterResilient, OfflineNoCacheError } from '../offline/constitutionRepository';
import WorkflowIcon from '../ui/icons/WorkflowIcon';
import { WORKFLOW_ICON_KEYS } from '../ui/icons/workflowIcons';

export default function ChapterDetailPage() {
  const { chapterId } = useParams();
  const [searchParams] = useSearchParams();
  const doc = searchParams.get('doc') || 'zanupf';
  const { readProgress } = useReaderData();
  const [chapter, setChapter] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const [syncHint, setSyncHint] = useState(null);

  useEffect(() => {
    setLoading(true);
    setError(null);
    loadChapterResilient(chapterId, {
      onCache: (data) => {
        setChapter(data);
        setLoading(false);
      },
    })
      .then((result) => {
        setChapter(result.data);
        if (result.offline) setSyncHint('Offline — saved chapter.');
        else if (result.stale) setSyncHint('Showing last saved copy.');
      })
      .catch((e) => {
        if (e instanceof OfflineNoCacheError) setError(e.message);
        else setError(catchMessage(e, 'Failed to load chapter.'));
      })
      .finally(() => setLoading(false));
  }, [chapterId]);

  if (loading) return <p className="p-4 text-sm text-app-subtle">Loading…</p>;
  if (error) return <p className="p-4 text-sm text-app-error">{error}</p>;

  const sections = chapter?.sections ?? [];

  function isRead(sectionId) {
    return Boolean(readProgress[`${doc}:${sectionId}`]);
  }

  return (
    <div className="p-4">
      {syncHint ? (
        <p className="mb-3 text-xs text-amber-200">{syncHint}</p>
      ) : null}
      <h2 className="mb-4 text-lg font-bold">{chapter?.title}</h2>
      <div className="space-y-1">
        {sections.map((s) => (
          <Link
            key={s.id}
            to={`/constitutions/sections/${s.id}?doc=${doc}&chapterId=${chapterId}`}
            className="flex items-center gap-2 rounded-lg border border-app-border bg-app-card px-3 py-2 text-sm hover:border-app-gold/40"
          >
            {isRead(s.id) ? (
              <WorkflowIcon iconKey="system.checkmark" size={14} aria-label="Read" />
            ) : (
              <span className="w-3.5 shrink-0" />
            )}
            <span className="flex-1">{s.title}</span>
          </Link>
        ))}
      </div>
    </div>
  );
}
