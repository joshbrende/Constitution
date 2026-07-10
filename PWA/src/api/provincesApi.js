import { apiClient } from './client';

export async function getProvinces() {
  const { data } = await apiClient.get('/provinces');
  return data?.data ?? [];
}
