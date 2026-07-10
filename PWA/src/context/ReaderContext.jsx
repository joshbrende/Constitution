import { createContext, useCallback, useContext, useEffect, useMemo, useState } from 'react';

const READER_PREFS_KEY = '@constitution_reader_prefs';

const THEMES = {
  original: { id: 'original', label: 'Original', bg: '#020617', text: '#f9fafb', accent: '#facc15' },
  quiet: { id: 'quiet', label: 'Quiet', bg: '#1e293b', text: '#e2e8f0', accent: '#94a3b8' },
  paper: { id: 'paper', label: 'Paper', bg: '#f5f0e6', text: '#1c1917', accent: '#15803d' },
  bold: { id: 'bold', label: 'Bold', bg: '#0a0a0f', text: '#ffffff', accent: '#facc15' },
  calm: { id: 'calm', label: 'Calm', bg: '#ecfdf5', text: '#064e3b', accent: '#15803d' },
  focus: { id: 'focus', label: 'Focus', bg: '#000000', text: '#d4d4d8', accent: '#facc15' },
};

const defaultPrefs = {
  fontSize: 16,
  themeId: 'original',
  lineHeight: 1.6,
  fontFamily: 'system',
  textAlign: 'left',
};

const ReaderContext = createContext(null);

export function ReaderProvider({ children }) {
  const [prefs, setPrefs] = useState(defaultPrefs);

  useEffect(() => {
    try {
      const raw = localStorage.getItem(READER_PREFS_KEY);
      if (raw) setPrefs({ ...defaultPrefs, ...JSON.parse(raw) });
    } catch {
      // ignore
    }
  }, []);

  const updatePrefs = useCallback((patch) => {
    setPrefs((prev) => {
      const next = { ...prev, ...patch };
      try {
        localStorage.setItem(READER_PREFS_KEY, JSON.stringify(next));
      } catch {
        // ignore
      }
      return next;
    });
  }, []);

  const theme = THEMES[prefs.themeId] || THEMES.original;

  const value = useMemo(
    () => ({ prefs, updatePrefs, theme, themes: THEMES }),
    [prefs, updatePrefs, theme]
  );

  return <ReaderContext.Provider value={value}>{children}</ReaderContext.Provider>;
}

export function useReader() {
  const ctx = useContext(ReaderContext);
  if (!ctx) throw new Error('useReader must be used within ReaderProvider');
  return ctx;
}
