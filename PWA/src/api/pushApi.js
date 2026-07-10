/** Push subscription registration for PWA Web Push (VAPID). */
import { apiClient } from './client';

export async function registerWebPushOnServer(subscription) {
  const { endpoint, keys } = subscription;
  const { data } = await apiClient.post('/profile/web-push-subscription', {
    endpoint,
    keys,
    device_name: typeof navigator !== 'undefined' ? navigator.userAgent?.slice(0, 120) : undefined,
  });
  return data?.data ?? data;
}

export async function unregisterWebPushOnServer(endpoint) {
  if (!endpoint) return;
  await apiClient.delete('/profile/web-push-subscription', { data: { endpoint } });
}
