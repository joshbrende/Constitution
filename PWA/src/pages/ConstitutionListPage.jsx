import { useEffect, useState } from 'react';
import { Link, useNavigate, useSearchParams } from 'react-router-dom';
import coverZanupf from '../assets/constitution-cover.png';
import coverZimbabwe from '../assets/constitution-cover-2.png';
import { getAmendment3OfficialPdfMeta } from '../api/officialConstitutionApi';
import { searchSections } from '../api/constitutionApi';
import { absoluteMediaUrl } from '../api/client';
import { catchMessage } from '../lib/apiErrors';
import { useNetwork } from '../context/NetworkContext';
import WorkflowIcon from '../ui/icons/WorkflowIcon';
import { WORKFLOW_ICON_KEYS } from '../ui/icons/workflowIcons';
import { loadPartsResilient, OfflineNoCacheError } from '../offline/constitutionRepository';

const DOCS = {
  zanupf: {
    cover: coverZanupf,
    fullTitle: 'CONSTITUTION OF THE ZIMBABWE AFRICAN NATIONAL UNION PATRIOTIC FRONT ZANUPF',
  },
  zimbabwe: {
    cover: coverZimbabwe,
    fullTitle: 'CONSTITUTION OF THE REPUBLIC OF ZIMBABWE (2013)',
  },
  amendment3: {
    cover: coverZimbabwe,
    fullTitle: 'CONSTITUTION AMENDMENT (NO 3) BILL 2026',
  },
};

export default function ConstitutionListPage() {
  const [searchParams, setSearchParams] = useSearchParams();
  const doc = searchParams.get('doc') || 'zanupf';
  const navigate = useNavigate();
  const { isOffline } = useNetwork();
  const [parts, setParts] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const [syncHint, setSyncHint] = useState(null);
  const [searchQuery, setSearchQuery] = useState('');
  const [searchResults, setSearchResults] = useState([]);
  const [searchLoading, setSearchLoading] = useState(false);
  const [amendmentOfficialPdf, setAmendmentOfficialPdf] = useState(null);

  useEffect(() => {
    if (doc !== 'amendment3') {
      setAmendmentOfficialPdf(null);
      return;
    }
    getAmendment3OfficialPdfMeta()
      .then((meta) => setAmendmentOfficialPdf(meta))
      .catch(() => setAmendmentOfficialPdf(null));
  }, [doc]);

  useEffect(() => {
    setLoading(true);
    setError(null);
    setSyncHint(null);
    loadPartsResilient(doc, {
      onCache: (cachedParts) => {
        setParts(cachedParts);
        setLoading(false);
        setSyncHint('Showing saved copy — updating when online…');
      },
    })
      .then((result) => {
        setParts(result.parts || []);
        if (result.source === 'cache' && result.offline) {
          setSyncHint('Offline — saved table of contents.');
        } else if (result.stale) {
          setSyncHint('Could not refresh — showing last saved copy.');
        } else {
          setSyncHint(null);
        }
      })
      .catch((e) => {
        if (e instanceof OfflineNoCacheError) {
          setError(e.message);
        } else {
          setError(catchMessage(e, 'Failed to load constitution.'));
        }
        setParts([]);
      })
      .finally(() => setLoading(false));
  }, [doc]);

  useEffect(() => {
    if (!searchQuery.trim() || isOffline) {
      setSearchResults([]);
      return undefined;
    }
    const t = setTimeout(async () => {
      setSearchLoading(true);
      try {
        const results = await searchSections(searchQuery.trim(), doc);
        setSearchResults(Array.isArray(results) ? results : []);
      } catch {
        setSearchResults([]);
      } finally {
        setSearchLoading(false);
      }
    }, 350);
    return () => clearTimeout(t);
  }, [searchQuery, doc, isOffline]);

  const meta = DOCS[doc] || DOCS.zanupf;

  return (
    <div className="p-4">
      <div className="mb-4 flex gap-2">
        <Link
          to="/constitutions/bookmarks"
          className="flex-1 rounded-lg border border-app-border bg-app-card py-2 text-center text-xs font-medium text-app-text"
        >
          Bookmarks
        </Link>
        <Link
          to="/constitutions/highlights"
          className="flex-1 rounded-lg border border-app-border bg-app-card py-2 text-center text-xs font-medium text-app-text"
        >
          Highlights
        </Link>
      </div>

      <div className="mb-4 flex gap-2 overflow-x-auto">
        {Object.keys(DOCS).map((key) => (
          <button
            key={key}
            type="button"
            onClick={() => setSearchParams({ doc: key })}
            className={`shrink-0 rounded-full px-3 py-1 text-xs font-medium ${
              doc === key ? 'bg-app-green text-white' : 'bg-app-surface text-app-subtle'
            }`}
          >
            {key === 'zanupf' ? 'ZANU PF' : key === 'zimbabwe' ? 'Zimbabwe' : 'Amendment 3'}
          </button>
        ))}
      </div>

      {doc !== 'amendment3' ? (
        <>
          <img src={meta.cover} alt="" className="mx-auto mb-3 h-32 object-contain" />
          <p className="mb-4 text-center text-xs font-medium leading-snug text-app-subtle">
            {meta.fullTitle}
          </p>
        </>
      ) : null}

      {doc === 'amendment3' ? (
        <div className="mb-4 rounded-xl border border-app-border bg-app-card p-4">
          <span className="inline-block rounded-full bg-app-gold px-2 py-0.5 text-[10px] font-bold uppercase text-black">
            Bill
          </span>
          <h2 className="mt-2 text-lg font-bold text-app-text">Amendment No. 3</h2>
          <p className="text-xs font-medium leading-snug text-app-subtle">{meta.fullTitle}</p>
          <p className="mt-2 text-xs text-app-subtle">
            Clause text is published from the government admin console. Open the official PDF when
            your administrators have uploaded it.
          </p>
          {amendmentOfficialPdf?.available && amendmentOfficialPdf?.url ? (
            <a
              href={absoluteMediaUrl(amendmentOfficialPdf.url)}
              target="_blank"
              rel="noopener noreferrer"
              className="mt-3 inline-flex items-center gap-2 rounded-full bg-app-gold px-4 py-2 text-sm font-semibold text-black"
            >
              <WorkflowIcon iconKey={WORKFLOW_ICON_KEYS.readerDocument} size={18} />
              Open official PDF
            </a>
          ) : null}
        </div>
      ) : null}

      {syncHint ? (
        <p className="mb-3 rounded-lg bg-amber-900/20 px-3 py-2 text-center text-xs text-amber-200">
          {syncHint}
        </p>
      ) : null}

      <input
        type="search"
        value={searchQuery}
        onChange={(e) => setSearchQuery(e.target.value)}
        placeholder={isOffline ? 'Search unavailable offline' : 'Search sections…'}
        disabled={isOffline}
        className="mb-4 w-full rounded-[10px] border border-app-border bg-app-bg px-3 py-2 text-sm disabled:opacity-50"
      />

      {searchQuery.trim() ? (
        <div className="space-y-2">
          {searchLoading ? <p className="text-sm text-app-subtle">Searching…</p> : null}
          {searchResults.map((s) => (
            <Link
              key={s.id}
              to={`/constitutions/sections/${s.id}?doc=${doc}`}
              className="block rounded-lg border border-app-border bg-app-card p-3 text-sm"
            >
              {s.title}
            </Link>
          ))}
        </div>
      ) : loading ? (
        <p className="text-sm text-app-subtle">Loading…</p>
      ) : error ? (
        <p className="text-sm text-app-error">{error}</p>
      ) : (
        <div className="space-y-4">
          {parts.map((part) => (
            <div key={part.id}>
              <h3 className="mb-2 text-sm font-semibold text-app-gold">{part.title}</h3>
              <div className="space-y-1">
                {(part.chapters ?? []).map((ch) => (
                  <button
                    key={ch.id}
                    type="button"
                    onClick={() => navigate(`/constitutions/chapters/${ch.id}?doc=${doc}`)}
                    className="block w-full rounded-lg border border-app-border bg-app-card px-3 py-2 text-left text-sm hover:border-app-gold/40"
                  >
                    {ch.title}
                  </button>
                ))}
              </div>
            </div>
          ))}
        </div>
      )}
    </div>
  );
}
