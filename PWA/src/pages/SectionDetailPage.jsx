import { useEffect, useMemo, useRef, useState } from 'react';
import { useNavigate, useParams, useSearchParams } from 'react-router-dom';
import { apiClient } from '../api/client';
import { catchMessage } from '../lib/apiErrors';
import { useReader } from '../context/ReaderContext';
import { useReaderData } from '../context/ReaderDataContext';
import { loadSectionResilient, OfflineNoCacheError } from '../offline/constitutionRepository';
import { applyTextHighlightsToHtml } from '../lib/readerHighlights';
import { exportSectionToPdf } from '../lib/sectionExport';
import { useGuest } from '../context/GuestContext';
import WorkflowIcon from '../ui/icons/WorkflowIcon';
import { WORKFLOW_ICON_KEYS } from '../ui/icons/workflowIcons';

const DOC_TITLES = {
  zanupf: 'ZANU PF Constitution',
  zimbabwe: 'Constitution of Zimbabwe',
  amendment3: 'Constitution Amendment (No. 3) Bill',
};

export default function SectionDetailPage() {
  const { sectionId } = useParams();
  const [searchParams] = useSearchParams();
  const doc = searchParams.get('doc') || 'zanupf';
  const navigate = useNavigate();
  const { isGuest } = useGuest();
  const { prefs, theme, updatePrefs, themes } = useReader();
  const {
    toggleBookmark,
    isBookmarked,
    markRead,
    addTextHighlight,
    getTextHighlightsForSection,
    getAllTextHighlights,
  } = useReaderData();
  const [section, setSection] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const [syncHint, setSyncHint] = useState(null);
  const [settingsOpen, setSettingsOpen] = useState(false);
  const [commentsOpen, setCommentsOpen] = useState(false);
  const [highlightsOpen, setHighlightsOpen] = useState(false);
  const [comments, setComments] = useState([]);
  const [commentsLoading, setCommentsLoading] = useState(false);
  const [commentInput, setCommentInput] = useState('');
  const [postingComment, setPostingComment] = useState(false);
  const [selection, setSelection] = useState(null);
  const [noteDraft, setNoteDraft] = useState('');
  const [noteModalOpen, setNoteModalOpen] = useState(false);
  const [exportingPdf, setExportingPdf] = useState(false);
  const readerRef = useRef(null);

  const sectionHighlights = section?.id ? getTextHighlightsForSection(section.id) : [];
  const allHighlights = getAllTextHighlights();
  const highlightedBody = useMemo(
    () => (section?.body ? applyTextHighlightsToHtml(section.body, sectionHighlights) : ''),
    [section?.body, sectionHighlights]
  );

  useEffect(() => {
    setLoading(true);
    setError(null);
    setSyncHint(null);
    loadSectionResilient(sectionId)
      .then((result) => {
        setSection(result.data);
        markRead(sectionId, doc);
        if (result.source === 'cache' && result.offline) {
          setSyncHint('Offline — showing saved copy.');
        } else if (result.stale) {
          setSyncHint('Could not refresh — showing last saved copy.');
        }
      })
      .catch((e) => {
        if (e instanceof OfflineNoCacheError) {
          setError(e.message);
        } else {
          setError(catchMessage(e, 'Failed to load section.'));
        }
      })
      .finally(() => setLoading(false));
  }, [sectionId, doc, markRead]);

  useEffect(() => {
    if (!commentsOpen || !section?.id) return;
    setCommentsLoading(true);
    apiClient
      .get(`/sections/${section.id}/comments`)
      .then((res) => setComments(res.data?.data ?? res.data ?? []))
      .catch(() => setComments([]))
      .finally(() => setCommentsLoading(false));
  }, [commentsOpen, section?.id]);

  function clearSelection() {
    setSelection(null);
    setNoteDraft('');
    setNoteModalOpen(false);
    window.getSelection()?.removeAllRanges();
  }

  function handleTextSelection() {
    const sel = window.getSelection();
    const text = sel?.toString().trim();
    if (!text || !readerRef.current) {
      setSelection(null);
      return;
    }
    const anchor = sel.anchorNode;
    if (!anchor || !readerRef.current.contains(anchor)) {
      setSelection(null);
      return;
    }
    const range = sel.getRangeAt(0);
    const rect = range.getBoundingClientRect();
    setSelection({
      text,
      top: rect.top + window.scrollY - 48,
      left: rect.left + rect.width / 2,
    });
  }

  function highlightMeta() {
    return {
      title: section?.title,
      logical_number: section?.logical_number,
      constitution_slug: doc,
    };
  }

  function handleHighlight(note) {
    if (!section?.id || !selection?.text) return;
    addTextHighlight(section.id, selection.text, note, highlightMeta());
    clearSelection();
  }

  async function postComment() {
    if (!section?.id || !commentInput.trim() || isGuest) return;
    setPostingComment(true);
    try {
      const res = await apiClient.post(`/sections/${section.id}/comments`, {
        body: commentInput.trim(),
      });
      const newComment = res.data?.data ?? res.data;
      setComments((prev) => [...prev, newComment]);
      setCommentInput('');
    } catch (e) {
      if (e?.response?.status === 401) {
        window.alert('Sign in to add comments.');
        navigate('/login');
      } else {
        window.alert(catchMessage(e, 'Could not post comment.'));
      }
    } finally {
      setPostingComment(false);
    }
  }

  async function copyText() {
    const el = readerRef.current;
    const text = el?.textContent || section?.title || '';
    try {
      await navigator.clipboard.writeText(text);
    } catch {
      window.alert('Copy not supported in this browser.');
    }
  }

  function printSection() {
    window.print();
  }

  function exportPdf() {
    if (!section) return;
    setExportingPdf(true);
    try {
      exportSectionToPdf({
        docTitle: DOC_TITLES[doc] || DOC_TITLES.zanupf,
        breadcrumb: [section.part?.title, section.chapter?.title].filter(Boolean).join(' › '),
        logicalNumber: section.logical_number,
        title: section.title,
        bodyHtml: highlightedBody,
      });
    } catch (e) {
      window.alert(e?.message || 'Could not export PDF. Try again.');
    } finally {
      setExportingPdf(false);
    }
  }

  if (loading) return <p className="p-4 text-sm text-app-subtle">Loading…</p>;
  if (error) return <p className="p-4 text-sm text-app-error">{error}</p>;

  const bookmarked = isBookmarked(Number(sectionId));

  function onBookmark() {
    toggleBookmark(Number(sectionId), {
      title: section.title,
      logical_number: section.logical_number,
      constitution_slug: doc,
    });
  }

  const readerStyle = {
    backgroundColor: theme.bg,
    color: theme.text,
    fontSize: `${prefs.fontSize}px`,
    lineHeight: prefs.lineHeight,
    fontFamily: prefs.fontFamily === 'serif' ? 'Georgia, serif' : 'system-ui, sans-serif',
    textAlign: prefs.textAlign,
  };

  return (
    <div style={readerStyle} className="min-h-full">
      <div className="sticky top-0 z-10 flex items-center justify-between border-b border-black/10 px-3 py-2 print:hidden">
        <button type="button" onClick={() => navigate(-1)} className="text-sm opacity-80">
          Back
        </button>
        <div className="flex gap-2">
          <button type="button" onClick={copyText} aria-label="Copy" className="p-1">
            <WorkflowIcon iconKey={WORKFLOW_ICON_KEYS.readerCopy} variant="current" size={18} />
          </button>
          <button type="button" onClick={printSection} aria-label="Print" className="p-1">
            <WorkflowIcon iconKey={WORKFLOW_ICON_KEYS.readerPrint} variant="current" size={18} />
          </button>
          <button
            type="button"
            onClick={exportPdf}
            disabled={exportingPdf}
            aria-label="Export PDF"
            className="p-1 disabled:opacity-50"
          >
            <WorkflowIcon iconKey={WORKFLOW_ICON_KEYS.certificateDownload} variant="current" size={18} />
          </button>
          <button type="button" onClick={() => setCommentsOpen(true)} aria-label="Comments" className="p-1">
            <WorkflowIcon iconKey={WORKFLOW_ICON_KEYS.readerComment} variant="current" size={18} />
          </button>
          <button
            type="button"
            onClick={() => setHighlightsOpen(true)}
            aria-label="Highlights"
            className="p-1"
          >
            <WorkflowIcon iconKey={WORKFLOW_ICON_KEYS.readerHighlight} variant="current" size={18} />
          </button>
          <button type="button" onClick={onBookmark} aria-label="Bookmark" className="p-1">
            <WorkflowIcon
              iconKey={WORKFLOW_ICON_KEYS.readerBookmark}
              variant="current"
              size={20}
              fill={bookmarked ? theme.accent : 'none'}
              color={theme.accent}
            />
          </button>
          <button type="button" onClick={() => setSettingsOpen((v) => !v)} className="text-sm">
            Aa
          </button>
        </div>
      </div>

      {syncHint ? (
        <p className="bg-amber-900/20 px-3 py-1 text-center text-xs text-amber-200 print:hidden">
          {syncHint}
        </p>
      ) : null}

      {settingsOpen ? (
        <div className="border-b border-black/10 p-3 text-sm print:hidden" style={{ color: theme.text }}>
          <label className="mb-2 block">
            Font size
            <input
              type="range"
              min={14}
              max={24}
              value={prefs.fontSize}
              onChange={(e) => updatePrefs({ fontSize: Number(e.target.value) })}
              className="ml-2 w-32"
            />
          </label>
          <div className="flex flex-wrap gap-2">
            {Object.values(themes).map((t) => (
              <button
                key={t.id}
                type="button"
                onClick={() => updatePrefs({ themeId: t.id })}
                className="rounded-full border px-2 py-0.5 text-xs"
                style={{
                  borderColor: prefs.themeId === t.id ? t.accent : 'transparent',
                  backgroundColor: t.bg,
                  color: t.text,
                }}
              >
                {t.label}
              </button>
            ))}
          </div>
        </div>
      ) : null}

      <article className="p-4">
        <h1 className="mb-4 text-lg font-bold">{section.title}</h1>
        {section.body ? (
          <div
            ref={readerRef}
            className="reader-body"
            onMouseUp={handleTextSelection}
            dangerouslySetInnerHTML={{ __html: highlightedBody }}
          />
        ) : (
          <p className="opacity-70">No content.</p>
        )}
      </article>

      {selection ? (
        <div
          className="fixed z-40 flex -translate-x-1/2 gap-1 rounded-lg border border-app-border bg-app-surface p-1 shadow-lg print:hidden"
          style={{ top: selection.top, left: selection.left }}
        >
          <button
            type="button"
            onClick={() => handleHighlight(null)}
            className="rounded-md bg-app-gold px-3 py-1 text-xs font-semibold text-black"
          >
            Highlight
          </button>
          <button
            type="button"
            onClick={() => setNoteModalOpen(true)}
            className="rounded-md bg-app-bg px-3 py-1 text-xs font-semibold text-app-text"
          >
            Add note
          </button>
          <button type="button" onClick={clearSelection} className="px-2 text-xs text-app-muted">
            ×
          </button>
        </div>
      ) : null}

      {noteModalOpen && selection ? (
        <div className="fixed inset-0 z-50 flex items-end bg-black/50 p-4 print:hidden sm:items-center">
          <div className="w-full max-w-md rounded-2xl border border-app-border bg-app-surface p-4">
            <p className="text-sm font-semibold text-app-text">Add note</p>
            <p className="mt-1 line-clamp-2 text-xs italic text-app-subtle">"{selection.text}"</p>
            <textarea
              rows={3}
              value={noteDraft}
              placeholder="Your note…"
              className="mt-3 w-full rounded-lg border border-app-border bg-app-bg px-3 py-2 text-sm"
              onChange={(e) => setNoteDraft(e.target.value)}
              autoFocus
            />
            <div className="mt-3 flex justify-end gap-2">
              <button
                type="button"
                onClick={clearSelection}
                className="rounded-full px-4 py-2 text-sm text-app-subtle"
              >
                Cancel
              </button>
              <button
                type="button"
                onClick={() => handleHighlight(noteDraft)}
                className="rounded-full bg-app-gold px-4 py-2 text-sm font-bold text-black"
              >
                Save
              </button>
            </div>
          </div>
        </div>
      ) : null}

      <div className="flex justify-between gap-2 border-t border-black/10 p-3 print:hidden">
        {section.prev_section_id ? (
          <button
            type="button"
            className="text-sm underline"
            onClick={() =>
              navigate(`/constitutions/sections/${section.prev_section_id}?doc=${doc}`)
            }
          >
            Previous
          </button>
        ) : (
          <span />
        )}
        {section.next_section_id ? (
          <button
            type="button"
            className="text-sm underline"
            onClick={() =>
              navigate(`/constitutions/sections/${section.next_section_id}?doc=${doc}`)
            }
          >
            Next
          </button>
        ) : null}
      </div>

      {highlightsOpen ? (
        <div className="fixed inset-0 z-50 flex flex-col bg-app-bg print:hidden">
          <div className="flex items-center justify-between border-b border-app-border px-4 py-3">
            <h2 className="font-bold">Highlights</h2>
            <button type="button" onClick={() => setHighlightsOpen(false)} className="text-app-gold">
              Close
            </button>
          </div>
          <div className="flex-1 overflow-y-auto p-4">
            {sectionHighlights.length === 0 ? (
              <p className="text-center text-sm text-app-muted">
                No highlights in this section. Select text and tap Highlight.
              </p>
            ) : (
              sectionHighlights.map((h) => (
                <div key={h.id} className="mb-3 rounded-lg border border-app-border bg-app-surface p-3">
                  <p className="text-sm italic">"{h.text}"</p>
                  {h.note ? <p className="mt-1 text-xs text-app-muted">{h.note}</p> : null}
                </div>
              ))
            )}
          </div>
          {allHighlights.length > 0 ? (
            <div className="border-t border-app-border p-3">
              <button
                type="button"
                onClick={() => {
                  setHighlightsOpen(false);
                  navigate('/constitutions/highlights');
                }}
                className="w-full rounded-lg bg-app-gold py-2 text-sm font-bold text-black"
              >
                View all highlights
              </button>
            </div>
          ) : null}
        </div>
      ) : null}

      {commentsOpen ? (
        <div className="fixed inset-0 z-50 flex flex-col bg-app-bg print:hidden">
          <div className="flex items-center justify-between border-b border-app-border px-4 py-3">
            <h2 className="font-bold">Comments</h2>
            <button type="button" onClick={() => setCommentsOpen(false)} className="text-app-gold">
              Close
            </button>
          </div>
          <div className="flex-1 overflow-y-auto p-4">
            {commentsLoading ? (
              <p className="text-sm text-app-subtle">Loading…</p>
            ) : comments.length === 0 ? (
              <p className="text-center text-sm text-app-muted">No comments yet.</p>
            ) : (
              comments.map((c) => (
                <div key={c.id} className="mb-3 rounded-lg border border-app-border bg-app-surface p-3">
                  <p className="text-xs font-semibold text-app-gold">{c.user?.name || 'Member'}</p>
                  <p className="mt-1 text-sm">{c.body}</p>
                </div>
              ))
            )}
          </div>
          {!isGuest ? (
            <div className="flex gap-2 border-t border-app-border p-3">
              <input
                value={commentInput}
                onChange={(e) => setCommentInput(e.target.value)}
                placeholder="Add a comment…"
                className="flex-1 rounded-lg border border-app-border bg-app-surface px-3 py-2 text-sm"
              />
              <button
                type="button"
                disabled={!commentInput.trim() || postingComment}
                onClick={postComment}
                className="rounded-lg bg-app-gold px-4 py-2 text-sm font-semibold text-black disabled:opacity-50"
              >
                Post
              </button>
            </div>
          ) : (
            <p className="border-t border-app-border p-3 text-center text-xs text-app-subtle">
              Sign in to comment.
            </p>
          )}
        </div>
      ) : null}
    </div>
  );
}
