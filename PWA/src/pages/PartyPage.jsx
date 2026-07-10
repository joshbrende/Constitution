import { useEffect, useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { getPartyProfile } from '../api/partyApi';
import { catchMessage } from '../lib/apiErrors';
import ScaleButton from '../components/ScaleButton';
import WorkflowIcon from '../ui/icons/WorkflowIcon';
import { WORKFLOW_ICON_KEYS } from '../ui/icons/workflowIcons';

const FALLBACK_BODY = `The Zimbabwe African National Union Patriotic Front (ZANU PF) is the mass revolutionary socialist Party in the emancipation process of the people of Zimbabwe.`;

function LeagueCard({ title, leaderName, leaderTitle, body, fallback, onPress }) {
  const displayBody = body?.trim() ? body : fallback;
  return (
    <ScaleButton
      onClick={onPress}
      className="block w-full rounded-xl border border-app-border bg-app-surface p-4 text-left"
    >
      <p className="font-semibold text-app-gold">{title}</p>
      {(leaderName || leaderTitle) && (
        <p className="text-sm text-app-subtle">
          {[leaderName, leaderTitle].filter(Boolean).join(' – ')}
        </p>
      )}
      <p className="mt-2 line-clamp-4 text-sm">{displayBody}</p>
      <span className="mt-2 inline-flex items-center gap-1 text-sm text-app-gold">
        Tap to read full
        <WorkflowIcon iconKey={WORKFLOW_ICON_KEYS.navChevronForward} size={14} />
      </span>
    </ScaleButton>
  );
}

export default function PartyPage() {
  const navigate = useNavigate();
  const [profile, setProfile] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    getPartyProfile()
      .then(setProfile)
      .catch((e) => setError(catchMessage(e, 'Failed to load Party profile.')))
      .finally(() => setLoading(false));
  }, []);

  if (loading) return <p className="p-4 text-sm text-app-subtle">Loading…</p>;
  if (error && !profile) return <p className="p-4 text-sm text-app-error">{error}</p>;

  const overview =
    (profile?.history && profile.history.trim()) || profile?.article_body || FALLBACK_BODY;

  return (
    <div className="space-y-4 p-4 pb-8">
      <div>
        <h2 className="text-xl font-bold">The Party</h2>
        <p className="text-sm text-app-subtle">
          Name, legal status, flag, headquarters, vision and mission of ZANU PF.
        </p>
      </div>
      <p className="whitespace-pre-wrap text-sm leading-relaxed">{overview}</p>

      <h3 className="font-semibold text-app-gold">Leagues</h3>
      <LeagueCard
        title="Veterans League"
        leaderName={profile?.veterans_league?.leader_name}
        leaderTitle={profile?.veterans_league?.leader_title}
        body={profile?.veterans_league?.body}
        fallback="The Veterans League mandate and description will appear here."
        onPress={() => navigate('/home/party/leagues/veterans')}
      />
      <LeagueCard
        title="Women's League"
        leaderName={profile?.womens_league?.leader_name}
        leaderTitle={profile?.womens_league?.leader_title}
        body={profile?.womens_league?.body}
        fallback="The Women's League mandate and description will appear here."
        onPress={() => navigate('/home/party/leagues/womens')}
      />
      <LeagueCard
        title="Youth League"
        leaderName={profile?.youth_league?.leader_name}
        leaderTitle={profile?.youth_league?.leader_title}
        body={profile?.youth_league?.body}
        fallback="The Youth League mandate and description will appear here."
        onPress={() => navigate('/home/party/leagues/youth')}
      />
    </div>
  );
}
