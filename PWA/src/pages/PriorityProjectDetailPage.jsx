import { useEffect, useState } from 'react';
import { useLocation, useNavigate, useParams } from 'react-router-dom';
import { absoluteMediaUrl } from '../api/client';
import { getPriorityProject } from '../api/priorityProjectsApi';
import { catchMessage } from '../lib/apiErrors';
import { useGuest } from '../context/GuestContext';

export default function PriorityProjectDetailPage() {
  const { projectId } = useParams();
  const { state } = useLocation();
  const navigate = useNavigate();
  const { isGuest } = useGuest();
  const [project, setProject] = useState(state?.project ?? null);
  const [loading, setLoading] = useState(!state?.project);
  const [error, setError] = useState(null);

  useEffect(() => {
    if (isGuest) navigate('/login', { replace: true });
  }, [isGuest, navigate]);

  useEffect(() => {
    if (isGuest || state?.project) return;
    getPriorityProject(projectId)
      .then(setProject)
      .catch((e) => setError(catchMessage(e, 'Failed to load project.')))
      .finally(() => setLoading(false));
  }, [projectId, state?.project, isGuest]);

  if (loading) return <p className="p-4 text-sm text-app-subtle">Loading…</p>;
  if (error) return <p className="p-4 text-sm text-app-error">{error}</p>;
  if (!project) return <p className="p-4 text-sm text-app-muted">Project not found.</p>;

  return (
    <article className="pb-8">
      {project.image_url ? (
        <img
          src={absoluteMediaUrl(project.image_url)}
          alt=""
          className="h-48 w-full object-cover"
        />
      ) : null}
      <div className="p-4">
        <h2 className="text-xl font-bold">{project.title}</h2>
        {project.body ? (
          <div className="reader-body mt-4 text-sm" dangerouslySetInnerHTML={{ __html: project.body }} />
        ) : project.summary ? (
          <p className="mt-4 text-sm leading-relaxed">{project.summary}</p>
        ) : null}
      </div>
    </article>
  );
}
