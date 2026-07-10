import { apiClient } from './client';

export async function getProfile() {
  const response = await apiClient.get('/profile');
  return response.data.data;
}

export async function updateProfile(payload) {
  const response = await apiClient.put('/profile', payload);
  return response.data.data;
}

export async function deleteAccount() {
  await apiClient.delete('/profile');
}
