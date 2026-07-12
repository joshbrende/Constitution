import { apiClient } from './client';

/**
 * @param {{ q?: string, province_id?: number, wing?: string, page?: number, per_page?: number }} params
 */
export async function searchMembers(params = {}) {
  const response = await apiClient.get('/members', { params });
  return {
    data: response.data?.data ?? [],
    meta: response.data?.meta ?? null,
  };
}
