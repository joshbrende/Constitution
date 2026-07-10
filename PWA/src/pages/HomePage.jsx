import { useCallback, useEffect, useRef, useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { absoluteMediaUrl, getApiBaseUrl } from '../api/client';
import { getAcademySummary } from '../api/academyApi';
import { getHomeBanners } from '../api/homeBannersApi';
import { getPresidium } from '../api/presidiumApi';
import { useGuest } from '../context/GuestContext';
import { useAppConfig } from '../context/AppConfigContext';
import ScaleButton from '../components/ScaleButton';
import { openHomeBanner } from '../lib/homeBannerNavigation';
import WorkflowIcon from '../ui/icons/WorkflowIcon';
import { WORKFLOW_ICON_KEYS } from '../ui/icons/workflowIcons';

/** Tile definitions — mirrors mobile/src/screens/HomeScreen.js MAIN_TILES */
const MAIN_TILES = [
  {
    id: 'presidium',
    title: 'Presidium',
    iconKey: WORKFLOW_ICON_KEYS.homePresidium,
    desc: 'President, Vice Presidents, leadership.',
    path: '/home/presidium',
  },
  {
    id: 'constitution',
    title: 'Constitutions',
    iconKey: WORKFLOW_ICON_KEYS.homeConstitution,
    desc: 'Read and study the Constitutions, article by article.',
    path: '/constitutions',
  },
  {
    id: 'academy',
    title: 'Academy',
    iconKey: WORKFLOW_ICON_KEYS.homeAcademy,
    desc: 'Learning paths, lessons, and assessments.',
    path: '/home/academy',
  },
  {
    id: 'library',
    title: 'Digital Library',
    iconKey: WORKFLOW_ICON_KEYS.homeLibrary,
    desc: 'Party documents and policy papers.',
    path: '/home/library',
  },
  {
    id: 'party',
    title: 'The Party',
    iconKey: WORKFLOW_ICON_KEYS.homeParty,
    desc: 'Name, flag, vision and mission of ZANU PF.',
    path: '/home/party',
  },
  {
    id: 'party-organs',
    title: 'Party Organs',
    iconKey: WORKFLOW_ICON_KEYS.homePartyOrgans,
    desc: 'Congress, Central Committee, Politburo, Leagues.',
    path: '/home/party-organs',
  },
  {
    id: 'dialogue',
    title: 'Chat',
    iconKey: WORKFLOW_ICON_KEYS.homeChat,
    desc: 'Dialogue and discussion on constitutional questions.',
    path: '/chat',
    authOnly: true,
    dialogueOnly: true,
  },
  {
    id: 'priority-projects',
    title: 'Priority projects',
    iconKey: WORKFLOW_ICON_KEYS.homePriorityProjects,
    desc: 'Strategic programmes and Vision 2030.',
    path: '/home/priority-projects',
    authOnly: true,
  },
];

function normalizeBanner(raw) {
  if (!raw || typeof raw !== 'object') return null;
  return {
    ...raw,
    id: raw.id ?? raw.slug ?? raw.title,
    image_url: absoluteMediaUrl(raw.image_url),
  };
}

function GridTile({ title, desc, iconKey, onPress }) {
  return (
    <ScaleButton
      onClick={onPress}
      className="rounded-xl border border-app-border bg-app-card p-3 text-left"
    >
      <div className="mb-1 flex items-start gap-2">
        <WorkflowIcon iconKey={iconKey} size={22} />
        <span className="text-sm font-semibold leading-tight text-app-text">{title}</span>
      </div>
      <p className="line-clamp-2 text-xs text-app-subtle">{desc}</p>
      <span className="mt-2 inline-block text-xs font-medium text-app-gold">Open</span>
    </ScaleButton>
  );
}

function BannerCard({ banner, onOpen }) {
  const clickable = Boolean(
    banner.internalPath ||
      banner.cta_url ||
      banner.cta_type === 'internal' ||
      banner.cta_type === 'external'
  );

  return (
    <button
      type="button"
      disabled={!clickable}
      onClick={() => clickable && onOpen(banner)}
      className="relative h-44 w-full shrink-0 snap-center overflow-hidden rounded-xl border border-app-border text-left disabled:opacity-100"
      style={{
        backgroundImage: banner.image_url ? `url(${banner.image_url})` : undefined,
        backgroundSize: 'cover',
        backgroundPosition: 'center',
        backgroundColor: '#15151f',
      }}
    >
      <div className="absolute inset-0 bg-linear-to-t from-black/85 via-black/45 to-black/20" />
      <div className="relative z-10 flex h-full flex-col justify-end p-3">
        <div className="mb-1 flex items-start gap-2">
          <WorkflowIcon
            iconKey={WORKFLOW_ICON_KEYS.systemAnnouncement}
            size={16}
            className="mt-0.5"
          />
          <p className="text-sm font-semibold leading-snug text-white">{banner.title}</p>
        </div>
        {banner.subtitle ? (
          <p className="line-clamp-2 text-xs text-slate-200">{banner.subtitle}</p>
        ) : null}
        {banner.cta_label ? (
          <span className="mt-2 inline-flex w-fit rounded-full bg-app-gold/90 px-2.5 py-0.5 text-[11px] font-semibold text-app-bg">
            {banner.cta_label}
          </span>
        ) : null}
      </div>
    </button>
  );
}

export default function HomePage() {
  const navigate = useNavigate();
  const { isGuest } = useGuest();
  const { dialogueEnabled } = useAppConfig();
  const [academySummary, setAcademySummary] = useState(null);
  const [banners, setBanners] = useState([]);
  const [bannerIndex, setBannerIndex] = useState(0);
  const [loading, setLoading] = useState(true);
  const [loadFailed, setLoadFailed] = useState(false);
  const [loadError, setLoadError] = useState(null);
  const carouselRef = useRef(null);
  const suppressScrollSync = useRef(false);

  const loadHomeData = useCallback(async () => {
    setLoading(true);
    setLoadFailed(false);
    setLoadError(null);
    try {
      const [summaryResult, bannerResult, presidiumResult] = await Promise.allSettled([
        getAcademySummary().catch(() => null),
        getHomeBanners(),
        getPresidium(),
      ]);

      setAcademySummary(summaryResult.status === 'fulfilled' ? summaryResult.value : null);

      const bannersFailed = bannerResult.status === 'rejected';
      const bannerData = bannerResult.status === 'fulfilled' ? bannerResult.value : [];
      const safeBanners = (Array.isArray(bannerData) ? bannerData : [])
        .map(normalizeBanner)
        .filter(Boolean);

      const presidium =
        presidiumResult.status === 'fulfilled' ? presidiumResult.value : { publications: [] };
      const featured =
        presidium.publications?.find((p) => p?.is_featured) ??
        presidium.publications?.[0] ??
        null;

      if (featured) {
        const injected = normalizeBanner({
          id: `presidium-publication-${featured.id}`,
          title: featured.title,
          subtitle: featured.author ?? 'Biography',
          image_url: featured.cover_url,
          cta_label: 'Read biography',
          cta_type: 'internal',
          cta_screen: 'Biography',
          cta_params: { publication: featured },
          internalPath: '/home/biography',
          publication: featured,
        });
        setBanners(injected ? [injected, ...safeBanners] : safeBanners);
      } else {
        setBanners(safeBanners);
      }

      setLoadFailed(bannersFailed);
      if (bannersFailed && bannerResult.reason) {
        setLoadError(bannerResult.reason);
      }
      setBannerIndex(0);
    } catch (e) {
      setAcademySummary(null);
      setBanners([]);
      setLoadFailed(true);
      setLoadError(e);
    } finally {
      setLoading(false);
    }
  }, []);

  useEffect(() => {
    loadHomeData();
  }, [loadHomeData]);

  useEffect(() => {
    if (banners.length <= 1) return undefined;
    const id = setInterval(() => {
      setBannerIndex((prev) => {
        const next = (prev + 1) % banners.length;
        const el = carouselRef.current;
        if (el) {
          suppressScrollSync.current = true;
          const width = el.clientWidth || 1;
          el.scrollTo({ left: next * width, behavior: 'smooth' });
          window.setTimeout(() => {
            suppressScrollSync.current = false;
          }, 450);
        }
        return next;
      });
    }, 6000);
    return () => clearInterval(id);
  }, [banners]);

  function onCarouselScroll(e) {
    if (suppressScrollSync.current || banners.length <= 1) return;
    const el = e.currentTarget;
    const width = el.clientWidth || 1;
    const next = Math.round(el.scrollLeft / width);
    if (next !== bannerIndex && next >= 0 && next < banners.length) {
      setBannerIndex(next);
    }
  }

  function openTile(tile) {
    if (tile.authOnly && isGuest) {
      navigate('/login');
      return;
    }
    if (tile.phase) {
      navigate('/coming-soon', { state: { feature: tile.title, phase: tile.phase } });
      return;
    }
    navigate(tile.path);
  }

  return (
    <div className="px-4 pb-6 pt-3">
      {loading ? <div className="mb-4 h-44 animate-pulse rounded-xl bg-app-surface" /> : null}

      {!loading && loadFailed ? (
        <div className="mb-4 flex gap-2 rounded-xl border border-amber-700/60 bg-amber-950/40 p-3">
          <WorkflowIcon iconKey={WORKFLOW_ICON_KEYS.systemOffline} size={18} className="mt-0.5" />
          <div className="min-w-0 flex-1">
            <p className="text-sm text-amber-100">
              Could not reach the API. Banners and some features need the backend running.
            </p>
            <p className="mt-1 font-mono text-[11px] text-amber-200/80">API: {getApiBaseUrl()}</p>
            {loadError?.userMessage || loadError?.message ? (
              <p className="mt-1 text-[11px] text-amber-200/80">
                {loadError.userMessage || loadError.message}
              </p>
            ) : null}
            <button
              type="button"
              onClick={loadHomeData}
              className="mt-2 rounded-full border border-app-gold px-3 py-1 text-xs font-semibold text-app-gold"
            >
              Retry
            </button>
          </div>
        </div>
      ) : null}

      {!loading && !loadFailed && banners.length === 0 ? (
        <div className="mb-4 flex gap-2 rounded-xl border border-app-border bg-app-card p-3">
          <WorkflowIcon
            iconKey={WORKFLOW_ICON_KEYS.systemAnnouncement}
            size={18}
            className="mt-0.5"
          />
          <div className="min-w-0 flex-1">
            <p className="text-sm text-app-subtle">
              No announcements yet. Tap Retry after the backend has been seeded.
            </p>
            <button
              type="button"
              onClick={loadHomeData}
              className="mt-2 rounded-full border border-app-gold px-3 py-1 text-xs font-semibold text-app-gold"
            >
              Retry
            </button>
          </div>
        </div>
      ) : null}

      {!loading && banners.length > 0 ? (
        <div className="mb-4">
          <div
            ref={carouselRef}
            onScroll={onCarouselScroll}
            className="flex snap-x snap-mandatory gap-0 overflow-x-auto scroll-smooth scrollbar-none"
          >
            {banners.map((banner) => (
              <div key={banner.id} className="w-full shrink-0 snap-center px-0">
                <BannerCard banner={banner} onOpen={(b) => openHomeBanner(navigate, b)} />
              </div>
            ))}
          </div>
          {banners.length > 1 ? (
            <div className="mt-2 flex items-center justify-center gap-1.5">
              {banners.map((b, i) => (
                <span
                  key={`dot-${b.id}`}
                  className={`h-1.5 rounded-full transition ${
                    i === bannerIndex ? 'w-4 bg-app-gold' : 'w-1.5 bg-app-muted'
                  }`}
                />
              ))}
            </div>
          ) : null}
        </div>
      ) : null}

      {academySummary ? (
        <div className="mb-4 rounded-xl border border-app-border bg-app-card p-3">
          <div className="mb-1 flex items-center gap-2">
            <WorkflowIcon iconKey={WORKFLOW_ICON_KEYS.homeAcademy} size={16} />
            <p className="text-xs uppercase tracking-wide text-app-gold">Academy</p>
          </div>
          <p className="mt-1 text-sm font-semibold">{academySummary.headline ?? 'Your learning'}</p>
          {academySummary.subheadline ? (
            <p className="text-xs text-app-subtle">{academySummary.subheadline}</p>
          ) : null}
        </div>
      ) : null}

      <div className="grid grid-cols-2 gap-3">
        {MAIN_TILES.filter(
          (tile) =>
            (!tile.authOnly || !isGuest) && (!tile.dialogueOnly || dialogueEnabled)
        ).map((tile) => (
          <GridTile key={tile.id} {...tile} onPress={() => openTile(tile)} />
        ))}
      </div>

      <a
        href="https://www.zanupf.org.zw/"
        target="_blank"
        rel="noopener noreferrer"
        className="mt-4 flex items-center justify-center gap-2 rounded-xl border border-app-border p-3 text-center text-sm text-app-gold"
      >
        <WorkflowIcon iconKey={WORKFLOW_ICON_KEYS.homeParty} size={18} />
        Official Party website
      </a>
    </div>
  );
}
