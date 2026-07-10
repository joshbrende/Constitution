import { useEffect, useState } from 'react';
import { useLocation, useNavigate, useParams } from 'react-router-dom';
import { getPartyOrgan, getPartyOrgans } from '../api/partyOrgansApi';
import { catchMessage } from '../lib/apiErrors';
import ScaleButton from '../components/ScaleButton';
import WorkflowIcon from '../ui/icons/WorkflowIcon';
import { WORKFLOW_ICON_KEYS } from '../ui/icons/workflowIcons';

export default function PartyOrgansPage() {
  const navigate = useNavigate();
  const [organs, setOrgans] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    getPartyOrgans()
      .then((data) => setOrgans(Array.isArray(data) ? data : []))
      .catch((e) => setError(catchMessage(e, 'Failed to load party organs.')))
      .finally(() => setLoading(false));
  }, []);

  if (loading) return <p className="p-4 text-sm text-app-subtle">Loading…</p>;
  if (error && organs.length === 0) return <p className="p-4 text-sm text-app-error">{error}</p>;

  return (
    <div className="space-y-4 p-4 pb-8">
      <div>
        <h2 className="text-xl font-bold">Party Organs</h2>
        <p className="text-sm text-app-subtle">
          Principal organs: Congress, Central Committee, Politburo, Leagues, and structures.
        </p>
      </div>
      {organs.map((organ) => (
        <ScaleButton
          key={organ.id}
          onClick={() => navigate(`/home/party-organs/${organ.id}`, { state: { title: organ.name } })}
          className="flex w-full items-center justify-between rounded-xl border border-app-border bg-app-surface p-4 text-left"
        >
          <div className="flex min-w-0 flex-1 items-center gap-3">
            <WorkflowIcon iconKey={WORKFLOW_ICON_KEYS.partyOrgan} size={22} />
            <div className="min-w-0">
              <p className="font-semibold">{organ.name}</p>
              {organ.summary ? <p className="line-clamp-2 text-xs text-app-subtle">{organ.summary}</p> : null}
            </div>
          </div>
          <WorkflowIcon iconKey={WORKFLOW_ICON_KEYS.navChevronForward} variant="muted" size={18} />
        </ScaleButton>
      ))}
    </div>
  );
}

export function PartyOrganDetailPage() {
  const { organId } = useParams();
  const { state } = useLocation();
  const [organ, setOrgan] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    getPartyOrgan(organId)
      .then(setOrgan)
      .catch((e) => setError(catchMessage(e, 'Failed to load organ.')))
      .finally(() => setLoading(false));
  }, [organId]);

  if (loading) return <p className="p-4 text-sm text-app-subtle">Loading…</p>;
  if (error) return <p className="p-4 text-sm text-app-error">{error}</p>;

  return (
    <article className="p-4 pb-8">
      <h2 className="mb-4 text-xl font-bold">{organ?.name || state?.title}</h2>
      {organ?.body ? (
        <div className="reader-body text-sm" dangerouslySetInnerHTML={{ __html: organ.body }} />
      ) : (
        <p className="text-sm text-app-muted">No content available.</p>
      )}
    </article>
  );
}
