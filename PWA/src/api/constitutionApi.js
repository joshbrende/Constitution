import { apiClient } from './client';

/** Phase 1: online-only constitution API (offline cache in Phase 3). */
export async function fetchParts(doc) {
  const { data } = await apiClient.get('/parts', { params: { doc } });
  return data?.data ?? data ?? [];
}

export async function fetchChapter(chapterId) {
  const { data } = await apiClient.get(`/chapters/${chapterId}`);
  return data?.data ?? data;
}

export async function fetchSection(sectionId) {
  const { data } = await apiClient.get(`/sections/${sectionId}`);
  return data?.data ?? data;
}

export async function searchSections(query, doc) {
  const { data } = await apiClient.get('/sections/search', { params: { q: query, doc } });
  return data?.data ?? data ?? [];
}
