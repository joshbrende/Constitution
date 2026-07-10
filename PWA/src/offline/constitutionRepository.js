import { apiClient } from '../api/client';
import { readCache, writeCache } from './db';

export class OfflineNoCacheError extends Error {
  constructor(message) {
    super(message);
    this.name = 'OfflineNoCacheError';
    this.code = 'OFFLINE_NO_CACHE';
  }
}

function isOnline() {
  return typeof navigator !== 'undefined' ? navigator.onLine : true;
}

function normalizeParts(data) {
  if (Array.isArray(data)) return data;
  if (Array.isArray(data?.data)) return data.data;
  return [];
}

/** Ported from mobile/src/offline/constitutionRepository.js */
export async function loadPartsResilient(doc, options = {}) {
  const cached = await readCache('parts', doc);
  const hasCache = Boolean(cached?.payload?.length);

  if (hasCache && options.onCache) options.onCache(cached.payload);

  if (!isOnline()) {
    if (hasCache) {
      return { parts: cached.payload, source: 'cache', offline: true, stale: true, savedAt: cached.savedAt };
    }
    throw new OfflineNoCacheError(
      'No saved copy of this constitution. Open Constitutions once while online to download.'
    );
  }

  try {
    const res = await apiClient.get('/parts', { params: { doc } });
    const parts = normalizeParts(res.data);
    await writeCache('parts', doc, parts);
    return { parts, source: 'network', offline: false, stale: false, savedAt: new Date().toISOString() };
  } catch (err) {
    if (hasCache) {
      return { parts: cached.payload, source: 'cache', offline: false, stale: true, savedAt: cached.savedAt, error: err };
    }
    throw err;
  }
}

export async function loadChapterResilient(chapterId, options = {}) {
  const key = String(chapterId);
  const cached = await readCache('chapters', key);
  if (cached?.payload && options.onCache) options.onCache(cached.payload);

  if (!isOnline()) {
    if (cached?.payload) {
      return { data: cached.payload, source: 'cache', offline: true, stale: true };
    }
    throw new OfflineNoCacheError('Chapter not available offline. View it once while online.');
  }

  try {
    const res = await apiClient.get(`/chapters/${chapterId}`);
    const data = res.data?.data ?? res.data;
    await writeCache('chapters', key, data);
    return { data, source: 'network', offline: false, stale: false };
  } catch (err) {
    if (cached?.payload) {
      return { data: cached.payload, source: 'cache', stale: true, error: err };
    }
    throw err;
  }
}

export async function loadSectionResilient(sectionId, options = {}) {
  const key = String(sectionId);
  const cached = await readCache('sections', key);
  if (cached?.payload && options.onCache) options.onCache(cached.payload);

  if (!isOnline()) {
    if (cached?.payload) {
      return { data: cached.payload, source: 'cache', offline: true, stale: true };
    }
    throw new OfflineNoCacheError('Section not available offline. Open it once while online.');
  }

  try {
    const res = await apiClient.get(`/sections/${sectionId}`);
    const data = res.data?.data ?? res.data;
    await writeCache('sections', key, data);
    return { data, source: 'network', offline: false, stale: false };
  } catch (err) {
    if (cached?.payload) {
      return { data: cached.payload, source: 'cache', stale: true, error: err };
    }
    throw err;
  }
}
