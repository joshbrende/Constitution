import { apiClient } from './client';

export async function getPortalNotifications() {
  const { data } = await apiClient.get('/portal-notifications');
  return data.data;
}

export async function markPortalNotificationRead(notificationId) {
  const { data } = await apiClient.post(`/portal-notifications/${notificationId}/read`);
  return data.data;
}

export async function markAllPortalNotificationsRead() {
  const { data } = await apiClient.post('/portal-notifications/read-all');
  return data.data;
}
