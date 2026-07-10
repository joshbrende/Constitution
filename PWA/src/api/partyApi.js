import { apiClient } from './client';

export async function getPartyProfile() {
  const { data } = await apiClient.get('/party/profile');
  return data.data;
}
