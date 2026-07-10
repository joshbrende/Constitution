import { createContext, useCallback, useContext, useEffect, useMemo, useState } from 'react';

const KEYS = {
  bookmarks: '@constitution_bookmarks',
  textHighlights: '@constitution_text_highlights',
  readProgress: '@constitution_read_progress',
};

const ReaderDataContext = createContext(null);

function loadJson(key, fallback) {
  try {
    const raw = localStorage.getItem(key);
    return raw ? JSON.parse(raw) : fallback;
  } catch {
    return fallback;
  }
}

function parseBookmarks(raw) {
  const map = new Map();
  if (!Array.isArray(raw)) return map;
  raw.forEach((entry) => {
    if (Array.isArray(entry)) {
      const meta = entry[1] || {};
      if (typeof meta === 'object' && !meta.constitution_slug) {
        meta.constitution_slug = meta.doc || 'zanupf';
      }
      map.set(Number(entry[0]), meta);
      return;
    }
    if (entry && typeof entry === 'object' && entry.section_id != null) {
      map.set(Number(entry.section_id), {
        title: entry.title || 'Section',
        logical_number: entry.logical_number || '',
        constitution_slug: entry.doc || entry.constitution_slug || 'zanupf',
      });
    }
  });
  return map;
}

function serializeBookmarks(map) {
  return [...map.entries()].map(([id, meta]) => [id, meta]);
}

export function ReaderDataProvider({ children }) {
  const [bookmarks, setBookmarks] = useState(() => new Map());
  const [textHighlights, setTextHighlights] = useState({});
  const [readProgress, setReadProgress] = useState({});

  useEffect(() => {
    setBookmarks(parseBookmarks(loadJson(KEYS.bookmarks, [])));
    setTextHighlights(loadJson(KEYS.textHighlights, {}));
    setReadProgress(loadJson(KEYS.readProgress, {}));
  }, []);

  const persist = useCallback((key, value) => {
    try {
      localStorage.setItem(key, JSON.stringify(value));
    } catch {
      // ignore
    }
  }, []);

  const toggleBookmark = useCallback(
    (sectionId, meta = {}) => {
      const id = Number(sectionId?.id ?? sectionId);
      setBookmarks((prev) => {
        const next = new Map(prev);
        if (next.has(id)) {
          next.delete(id);
        } else {
          next.set(id, {
            title: meta.title || sectionId?.title || 'Section',
            logical_number: meta.logical_number || sectionId?.logical_number || '',
            constitution_slug: meta.constitution_slug || meta.doc || sectionId?.doc || 'zanupf',
          });
        }
        persist(KEYS.bookmarks, serializeBookmarks(next));
        return next;
      });
    },
    [persist]
  );

  const isBookmarked = useCallback((sectionId) => bookmarks.has(Number(sectionId)), [bookmarks]);

  const getBookmarksList = useCallback(
    () => [...bookmarks.entries()].map(([id, meta]) => ({ id, ...meta })),
    [bookmarks]
  );

  const getTextHighlightsForSection = useCallback(
    (sectionId) => textHighlights[String(sectionId)] || [],
    [textHighlights]
  );

  const addTextHighlight = useCallback(
    (sectionId, text, note, meta = {}) => {
      const sid = String(sectionId);
      const trimmed = (text || '').trim();
      if (!trimmed) return;
      setTextHighlights((prev) => {
        const existing = prev[sid] || [];
        const sameText = existing.find(
          (h) => (h.text || '').trim().toLowerCase() === trimmed.toLowerCase()
        );
        if (sameText) {
          if (note != null && note !== '') {
            const nextForSection = existing.map((h) =>
              (h.text || '').trim().toLowerCase() === trimmed.toLowerCase()
                ? {
                    ...h,
                    note: note.trim() || null,
                    sectionTitle: meta.title ?? h.sectionTitle,
                    sectionLogicalNumber: meta.logical_number ?? h.sectionLogicalNumber,
                    constitution_slug: meta.constitution_slug ?? h.constitution_slug ?? 'zanupf',
                  }
                : h
            );
            const next = { ...prev, [sid]: nextForSection };
            persist(KEYS.textHighlights, next);
            return next;
          }
          return prev;
        }
        const item = {
          id: Date.now().toString(),
          text: trimmed,
          note: note || null,
          sectionTitle: meta.title ?? null,
          sectionLogicalNumber: meta.logical_number ?? null,
          constitution_slug: meta.constitution_slug ?? 'zanupf',
        };
        const next = { ...prev, [sid]: [...existing, item] };
        persist(KEYS.textHighlights, next);
        return next;
      });
    },
    [persist]
  );

  const getAllTextHighlights = useCallback(() => {
    const result = [];
    Object.entries(textHighlights).forEach(([sectionId, arr]) => {
      const list = Array.isArray(arr) ? arr : [];
      list.forEach((h) => {
        result.push({
          sectionId: Number(sectionId),
          id: h.id,
          text: h.text,
          note: h.note,
          sectionTitle: h.sectionTitle ?? null,
          sectionLogicalNumber: h.sectionLogicalNumber ?? null,
          constitution_slug: h.constitution_slug ?? 'zanupf',
        });
      });
    });
    return result;
  }, [textHighlights]);

  const updateTextHighlightNote = useCallback(
    (sectionId, highlightId, newNote) => {
      const sid = String(sectionId);
      setTextHighlights((prev) => {
        const existing = prev[sid] || [];
        const nextForSection = existing.map((h) =>
          String(h.id) === String(highlightId) ? { ...h, note: (newNote || '').trim() || null } : h
        );
        const next = { ...prev, [sid]: nextForSection };
        persist(KEYS.textHighlights, next);
        return next;
      });
    },
    [persist]
  );

  const removeTextHighlight = useCallback(
    (sectionId, highlightId) => {
      const sid = String(sectionId);
      setTextHighlights((prev) => {
        const existing = prev[sid] || [];
        const nextForSection = existing.filter((h) => String(h.id) !== String(highlightId));
        const next = nextForSection.length
          ? { ...prev, [sid]: nextForSection }
          : (() => {
              const copy = { ...prev };
              delete copy[sid];
              return copy;
            })();
        persist(KEYS.textHighlights, next);
        return next;
      });
    },
    [persist]
  );

  const markRead = useCallback(
    (sectionId, doc) => {
      setReadProgress((prev) => {
        const next = { ...prev, [`${doc}:${sectionId}`]: Date.now() };
        persist(KEYS.readProgress, next);
        return next;
      });
    },
    [persist]
  );

  const value = useMemo(
    () => ({
      bookmarks,
      readProgress,
      toggleBookmark,
      isBookmarked,
      getBookmarksList,
      textHighlights,
      getTextHighlightsForSection,
      addTextHighlight,
      getAllTextHighlights,
      updateTextHighlightNote,
      removeTextHighlight,
      markRead,
    }),
    [
      bookmarks,
      readProgress,
      toggleBookmark,
      isBookmarked,
      getBookmarksList,
      textHighlights,
      getTextHighlightsForSection,
      addTextHighlight,
      getAllTextHighlights,
      updateTextHighlightNote,
      removeTextHighlight,
      markRead,
    ]
  );

  return <ReaderDataContext.Provider value={value}>{children}</ReaderDataContext.Provider>;
}

export function useReaderData() {
  const ctx = useContext(ReaderDataContext);
  if (!ctx) throw new Error('useReaderData must be used within ReaderDataProvider');
  return ctx;
}
