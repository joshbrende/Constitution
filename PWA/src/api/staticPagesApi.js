import { apiClient } from './client';

export async function getStaticPage(slug) {
  const { data } = await apiClient.get(`/pages/${slug}`);
  return data?.data ?? null;
}
