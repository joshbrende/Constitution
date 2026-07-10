import { apiClient } from './client';

export async function getAppConfig() {
  const { data } = await apiClient.get('/app-config');
  return data?.data ?? data ?? {};
}
