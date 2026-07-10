import { getAppConfig } from '../api/appConfigApi';
import { registerWebPushOnServer, unregisterWebPushOnServer } from '../api/pushApi';

const ENDPOINT_KEY = '@web_push_endpoint';

function urlBase64ToUint8Array(base64String) {
  const padding = '='.repeat((4 - (base64String.length % 4)) % 4);
  const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
  const raw = window.atob(base64);
  const arr = new Uint8Array(raw.length);
  for (let i = 0; i < raw.length; i += 1) {
    arr[i] = raw.charCodeAt(i);
  }
  return arr;
}

export function getStoredWebPushEndpoint() {
  try {
    return localStorage.getItem(ENDPOINT_KEY);
  } catch {
    return null;
  }
}

function storeEndpoint(endpoint) {
  try {
    if (endpoint) localStorage.setItem(ENDPOINT_KEY, endpoint);
    else localStorage.removeItem(ENDPOINT_KEY);
  } catch {
    // ignore
  }
}

export function isWebPushSupported() {
  return (
    typeof window !== 'undefined' &&
    'serviceWorker' in navigator &&
    'PushManager' in window &&
    'Notification' in window
  );
}

/**
 * Request permission, subscribe with VAPID key from app-config, register with API.
 * Safe to call after login; failures are non-fatal.
 */
export async function registerDeviceWebPush() {
  if (!isWebPushSupported()) return null;

  const config = await getAppConfig();
  const webpush = config?.webpush;
  if (!webpush?.enabled || !webpush?.public_key) return null;

  if (Notification.permission === 'denied') return null;
  if (Notification.permission === 'default') {
    const permission = await Notification.requestPermission();
    if (permission !== 'granted') return null;
  }

  const registration = await navigator.serviceWorker.ready;
  let subscription = await registration.pushManager.getSubscription();

  if (!subscription) {
    subscription = await registration.pushManager.subscribe({
      userVisibleOnly: true,
      applicationServerKey: urlBase64ToUint8Array(webpush.public_key),
    });
  }

  const json = subscription.toJSON();
  await registerWebPushOnServer(json);
  storeEndpoint(json.endpoint);

  return json;
}

export async function unregisterDeviceWebPush() {
  if (!isWebPushSupported()) return;

  const endpoint = getStoredWebPushEndpoint();
  try {
    if (endpoint) {
      await unregisterWebPushOnServer(endpoint);
    }
  } catch {
    // ignore API cleanup failures
  }

  try {
    const registration = await navigator.serviceWorker.ready;
    const subscription = await registration.pushManager.getSubscription();
    if (subscription) {
      await subscription.unsubscribe();
    }
  } catch {
    // ignore
  }

  storeEndpoint(null);
}
