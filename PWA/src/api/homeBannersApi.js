import { apiClient } from './client';

export async function getHomeBanners() {
  const { data } = await apiClient.get('/home-banners');
  return data?.data ?? [];
}
