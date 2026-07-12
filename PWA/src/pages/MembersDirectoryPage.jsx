import { useCallback, useEffect, useState } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { getProfile } from '../api/profileApi';
import { getProvinces } from '../api/provincesApi';
import { searchMembers } from '../api/membersApi';
import { catchMessage } from '../lib/apiErrors';
import { useGuest } from '../context/GuestContext';
import ScaleButton from '../components/ScaleButton';

function formatWings(wings) {
  if (!Array.isArray(wings) || wings.length === 0) return '—';
  return wings
    .map((w) => (w === 'main' ? 'Main' : `${w.charAt(0).toUpperCase()}${w.slice(1)}`))
    .join(', ');
}

export default function MembersDirectoryPage() {
  const navigate = useNavigate();
  const { isGuest } = useGuest();
  const [profile, setProfile] = useState(null);
  const [provinces, setProvinces] = useState([]);
  const [q, setQ] = useState('');
  const [provinceId, setProvinceId] = useState('');
  const [wing, setWing] = useState('');
  const [members, setMembers] = useState([]);
  const [meta, setMeta] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  const isFullMember = profile?.membership_standing === 'member';

  useEffect(() => {
    if (isGuest) {
      navigate('/login', { replace: true });
    }
  }, [isGuest, navigate]);

  useEffect(() => {
    Promise.all([getProfile(), getProvinces().catch(() => [])])
      .then(([u, provs]) => {
        setProfile(u);
        setProvinces(Array.isArray(provs) ? provs : []);
      })
      .catch((e) => setError(catchMessage(e, 'Failed to load profile.')))
      .finally(() => setLoading(false));
  }, []);

  const load = useCallback(async (overrides = {}) => {
    if (!isFullMember) return;
    setLoading(true);
    setError(null);
    const nextQ = overrides.q !== undefined ? overrides.q : q;
    const nextProvince = overrides.provinceId !== undefined ? overrides.provinceId : provinceId;
    const nextWing = overrides.wing !== undefined ? overrides.wing : wing;
    try {
      const result = await searchMembers({
        q: String(nextQ || '').trim() || undefined,
        province_id: nextProvince ? Number(nextProvince) : undefined,
        wing: nextWing || undefined,
        per_page: 25,
      });
      setMembers(result.data);
      setMeta(result.meta);
    } catch (e) {
      setError(catchMessage(e, 'Failed to search members.'));
      setMembers([]);
    } finally {
      setLoading(false);
    }
  }, [isFullMember, q, provinceId, wing]);

  useEffect(() => {
    if (profile && isFullMember) {
      load({ q: '', provinceId: '', wing: '' });
    }
    // Initial directory load once profile confirms full membership.
    // eslint-disable-next-line react-hooks/exhaustive-deps -- intentional one-shot after profile
  }, [profile, isFullMember]);

  if (isGuest) return null;

  if (!loading && profile && !isFullMember) {
    return (
      <div className="space-y-4 p-4 pb-8">
        <h1 className="text-lg font-semibold text-app-text">Members</h1>
        <p className="text-sm text-app-subtle">
          The member directory is available after your membership certificate is issued.
        </p>
        <Link to="/profile" className="text-sm text-app-gold underline">
          Back to profile
        </Link>
      </div>
    );
  }

  return (
    <div className="space-y-4 p-4 pb-8">
      <h1 className="text-lg font-semibold text-app-text">Members</h1>
      <p className="text-sm text-app-subtle">Search full members nationwide by name or membership number.</p>

      <form
        className="space-y-3"
        onSubmit={(e) => {
          e.preventDefault();
          load();
        }}
      >
        <input
          type="search"
          value={q}
          onChange={(e) => setQ(e.target.value)}
          placeholder="Name or membership number"
          className="w-full rounded-lg border border-app-border bg-app-surface px-3 py-2 text-sm text-app-text"
        />
        <div className="flex flex-col gap-2 sm:flex-row">
          <select
            value={provinceId}
            onChange={(e) => setProvinceId(e.target.value)}
            className="flex-1 rounded-lg border border-app-border bg-app-surface px-3 py-2 text-sm text-app-text"
          >
            <option value="">All provinces</option>
            {provinces.map((p) => (
              <option key={p.id} value={p.id}>
                {p.name}
              </option>
            ))}
          </select>
          <select
            value={wing}
            onChange={(e) => setWing(e.target.value)}
            className="flex-1 rounded-lg border border-app-border bg-app-surface px-3 py-2 text-sm text-app-text"
          >
            <option value="">All leagues</option>
            <option value="main">Main</option>
            <option value="youth">Youth</option>
            <option value="women">Women</option>
            <option value="veterans">Veterans</option>
          </select>
        </div>
        <ScaleButton type="submit" className="rounded-lg bg-app-gold px-4 py-2 text-sm font-medium text-black">
          Search
        </ScaleButton>
      </form>

      {error ? <p className="text-sm text-red-400">{error}</p> : null}
      {loading ? <p className="text-sm text-app-subtle">Loading…</p> : null}

      {!loading && members.length === 0 ? (
        <p className="text-sm text-app-subtle">No members found.</p>
      ) : (
        <ul className="divide-y divide-app-border rounded-lg border border-app-border bg-app-surface">
          {members.map((m) => (
            <li key={m.id} className="px-3 py-3">
              <p className="font-medium text-app-text">
                {m.name} {m.surname}
              </p>
              <p className="mt-0.5 font-mono text-xs text-app-subtle">{m.membership_number || '—'}</p>
              <p className="mt-1 text-xs text-app-subtle">
                {m.province?.name || 'Province unknown'} · {formatWings(m.active_wings)}
              </p>
            </li>
          ))}
        </ul>
      )}

      {meta?.total != null ? (
        <p className="text-xs text-app-subtle">
          Showing {members.length} of {meta.total}
        </p>
      ) : null}
    </div>
  );
}
