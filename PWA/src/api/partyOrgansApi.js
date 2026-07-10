import { apiClient } from './client';

export async function getPartyOrgans() {
  const { data } = await apiClient.get('/party-organs');
  return data.data;
}

export async function getPartyOrgan(id) {
  const { data } = await apiClient.get(`/party-organs/${id}`);
  return data.data;
}
