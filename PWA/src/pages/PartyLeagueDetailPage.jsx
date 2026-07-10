import { useEffect, useState } from 'react';
import { useParams } from 'react-router-dom';
import { getPartyProfile } from '../api/partyApi';
import { catchMessage } from '../lib/apiErrors';

const PROFILE_KEYS = { veterans: 'veterans_league', womens: 'womens_league', youth: 'youth_league' };
const TITLES = { veterans: 'Veterans League', womens: "Women's League", youth: 'Youth League' };
const FALLBACKS = {
  veterans: 'The Veterans League mandate and description will appear here.',
  womens: "The Women's League mandate and description will appear here.",
  youth: 'The Youth League mandate and description will appear here.',
};

function stripHtml(html) {
  if (!html || typeof html !== 'string') return '';
  const div = document.createElement('div');
  div.innerHTML = html;
  return div.textContent || div.innerText || '';
}

export default function PartyLeagueDetailPage() {
  const { league } = useParams();
  const leagueKey = ['veterans', 'womens', 'youth'].includes(league) ? league : null;
  const [profile, setProfile] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    if (!leagueKey) {
      setError('Invalid league.');
      setLoading(false);
      return;
    }
    getPartyProfile()
      .then(setProfile)
      .catch((e) => setError(catchMessage(e, 'Failed to load league.')))
      .finally(() => setLoading(false));
  }, [leagueKey]);

  if (loading) return <p className="p-4 text-sm text-app-subtle">Loading…</p>;
  if (error || !leagueKey) return <p className="p-4 text-sm text-app-error">{error || 'Not found.'}</p>;

  const leagueData = profile?.[PROFILE_KEYS[leagueKey]];
  const body = leagueData?.body ? stripHtml(leagueData.body) : FALLBACKS[leagueKey];

  return (
    <article className="p-4 pb-8">
      <h2 className="text-xl font-bold">{TITLES[leagueKey]}</h2>
      {(leagueData?.leader_name || leagueData?.leader_title) && (
        <p className="mt-2 text-sm text-app-subtle">
          {[leagueData.leader_name, leagueData.leader_title].filter(Boolean).join(' – ')}
        </p>
      )}
      <p className="mt-4 whitespace-pre-wrap text-sm leading-relaxed">{body}</p>
    </article>
  );
}
