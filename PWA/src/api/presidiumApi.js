import { apiClient } from './client';

export async function getPresidium() {
  const res = await apiClient.get('/presidium');
  const data = res.data?.data ?? {};
  return {
    members: Array.isArray(data.members) ? data.members : [],
    publications: Array.isArray(data.publications) ? data.publications : [],
  };
}
