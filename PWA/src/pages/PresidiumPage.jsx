import { useEffect, useState } from 'react';
import { Link, useLocation } from 'react-router-dom';
import { absoluteMediaUrl } from '../api/client';
import { getPresidium } from '../api/presidiumApi';
import { catchMessage } from '../lib/apiErrors';
import WorkflowIcon from '../ui/icons/WorkflowIcon';

function roleIconKey(title) {
  const t = (title || '').toLowerCase();
  if (t.includes('president') && !t.includes('vice')) return 'role.president';
  if (t.includes('vice')) return 'role.vicePresident';
  if (t.includes('chair')) return 'role.nationalChairperson';
  if (t.includes('secretary')) return 'role.secretaryGeneral';
  return 'role.fallback';
}

export default function PresidiumPage() {
  const [members, setMembers] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    getPresidium()
      .then((data) => setMembers(data.members ?? []))
      .catch((e) => setError(catchMessage(e, 'Failed to load presidium.')))
      .finally(() => setLoading(false));
  }, []);

  if (loading) {
    return <div className="p-4 text-sm text-app-subtle">Loading…</div>;
  }
  if (error) {
    return <div className="p-4 text-sm text-app-error">{error}</div>;
  }

  return (
    <div className="space-y-3 p-4">
      {members.map((m) => (
        <div
          key={m.id}
          className="flex gap-3 rounded-xl border border-app-border bg-app-card p-3"
        >
          {m.photo_url ? (
            <img
              src={absoluteMediaUrl(m.photo_url)}
              alt=""
              className="h-16 w-16 shrink-0 rounded-lg object-cover"
            />
          ) : (
            <div className="flex h-16 w-16 shrink-0 items-center justify-center rounded-lg bg-app-surface">
              <WorkflowIcon iconKey={roleIconKey(m.title)} size={28} />
            </div>
          )}
          <div>
            <p className="font-semibold">{m.name}</p>
            <p className="text-sm text-app-gold">{m.title}</p>
            {m.bio ? <p className="mt-1 line-clamp-3 text-xs text-app-subtle">{m.bio}</p> : null}
          </div>
        </div>
      ))}
    </div>
  );
}

export function BiographyPage() {
  const { state } = useLocation();
  const publication = state?.publication;

  if (!publication) {
    return (
      <div className="p-4">
        <Link to="/home/presidium" className="text-sm text-app-gold">
          View presidium
        </Link>
      </div>
    );
  }

  return (
    <article className="p-4">
      {publication.cover_url ? (
        <img
          src={absoluteMediaUrl(publication.cover_url)}
          alt=""
          className="mb-4 w-full rounded-xl object-cover"
        />
      ) : null}
      <h2 className="text-xl font-bold">{publication.title}</h2>
      {publication.author ? (
        <p className="text-sm text-app-gold">{publication.author}</p>
      ) : null}
      {publication.body ? (
        <div
          className="reader-body mt-4 text-sm leading-relaxed"
          dangerouslySetInnerHTML={{ __html: publication.body }}
        />
      ) : (
        <p className="mt-4 text-sm text-app-subtle">No biography text available.</p>
      )}
    </article>
  );
}
