import { useEffect, useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { absoluteMediaUrl } from '../api/client';
import { getPriorityProjects, likePriorityProject } from '../api/priorityProjectsApi';
import { catchMessage } from '../lib/apiErrors';
import { useGuest } from '../context/GuestContext';
import ScaleButton from '../components/ScaleButton';
import WorkflowIcon from '../ui/icons/WorkflowIcon';
import { WORKFLOW_ICON_KEYS } from '../ui/icons/workflowIcons';

export default function PriorityProjectsPage() {
  const navigate = useNavigate();
  const { isGuest } = useGuest();
  const [projects, setProjects] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const [likingId, setLikingId] = useState(null);

  useEffect(() => {
    if (isGuest) navigate('/login', { replace: true });
  }, [isGuest, navigate]);

  useEffect(() => {
    if (isGuest) return;
    getPriorityProjects()
      .then((data) => setProjects(Array.isArray(data) ? data : []))
      .catch((e) => setError(catchMessage(e, 'Failed to load projects.')))
      .finally(() => setLoading(false));
  }, [isGuest]);

  async function handleLike(e, item) {
    e.stopPropagation();
    if (isGuest) {
      navigate('/login');
      return;
    }
    if (!item || item.liked) return;
    setLikingId(item.id);
    try {
      setProjects((prev) =>
        prev.map((p) =>
          p.id === item.id ? { ...p, liked: true, likes_count: (p.likes_count || 0) + 1 } : p
        )
      );
      const res = await likePriorityProject(item.id);
      if (res) {
        setProjects((prev) =>
          prev.map((p) =>
            p.id === item.id ? { ...p, liked: !!res.liked, likes_count: res.likes_count } : p
          )
        );
      }
    } catch (err) {
      window.alert(catchMessage(err, 'Could not save like.'));
      setProjects((prev) =>
        prev.map((p) =>
          p.id === item.id
            ? { ...p, liked: false, likes_count: Math.max((p.likes_count || 1) - 1, 0) }
            : p
        )
      );
    } finally {
      setLikingId(null);
    }
  }

  if (loading) return <p className="p-4 text-sm text-app-subtle">Loading…</p>;
  if (error) return <p className="p-4 text-sm text-app-error">{error}</p>;

  return (
    <div className="space-y-4 p-4 pb-8">
      <div>
        <h2 className="text-xl font-bold">Priority projects</h2>
        <p className="text-sm text-app-subtle">Strategic programmes and Vision 2030.</p>
      </div>
      {projects.map((item) => (
        <ScaleButton
          key={item.id}
          onClick={() => navigate(`/home/priority-projects/${item.id}`, { state: { project: item } })}
          className="block w-full overflow-hidden rounded-xl border border-app-border bg-app-surface text-left"
        >
          {item.image_url ? (
            <img
              src={absoluteMediaUrl(item.image_url)}
              alt=""
              className="h-36 w-full object-cover"
            />
          ) : null}
          <div className="p-3">
            <div className="flex items-start justify-between gap-2">
              <p className="font-semibold">{item.title}</p>
              <button
                type="button"
                disabled={likingId === item.id || item.liked}
                onClick={(e) => handleLike(e, item)}
                className="flex shrink-0 items-center gap-1 text-sm"
              >
                <WorkflowIcon
                  iconKey={WORKFLOW_ICON_KEYS.priorityHeart}
                  size={16}
                  variant={item.liked ? 'danger' : 'gold'}
                  fill={item.liked ? 'currentColor' : 'none'}
                />
                {item.likes_count ?? 0}
              </button>
            </div>
            {item.summary ? <p className="mt-1 line-clamp-3 text-xs text-app-subtle">{item.summary}</p> : null}
          </div>
        </ScaleButton>
      ))}
    </div>
  );
}
