import { useEffect, useRef } from 'react';
import { usePortalNotifications } from '../context/PortalNotificationsContext';

/** Browser notification permission helpers (Web Push fallback). */

export function getNotificationPermission() {
  if (typeof window === 'undefined' || !('Notification' in window)) return 'unsupported';
  return Notification.permission;
}

export async function requestNotificationPermission() {
  if (getNotificationPermission() === 'unsupported') {
    return 'unsupported';
  }
  if (Notification.permission === 'granted') return 'granted';
  if (Notification.permission === 'denied') return 'denied';
  try {
    return await Notification.requestPermission();
  } catch {
    return 'denied';
  }
}

export function notificationPermissionLabel(permission) {
  switch (permission) {
    case 'granted':
      return 'Enabled';
    case 'denied':
      return 'Blocked in browser settings';
    case 'default':
      return 'Not enabled';
    case 'unsupported':
      return 'Not supported in this browser';
    default:
      return permission;
  }
}

/**
 * Browser notifications when unread count rises and tab is hidden.
 * Fallback until backend supports Web Push (VAPID).
 */
export function useBrowserNotificationAlerts() {
  const { unreadCount, messages } = usePortalNotifications();
  const prevCount = useRef(0);
  const permissionRequested = useRef(false);

  useEffect(() => {
    if (typeof window === 'undefined' || !('Notification' in window)) return;

    if (Notification.permission === 'default' && !permissionRequested.current && unreadCount > 0) {
      permissionRequested.current = true;
      Notification.requestPermission().catch(() => {});
    }

    if (Notification.permission !== 'granted') return;
    if (document.visibilityState === 'visible') {
      prevCount.current = unreadCount;
      return;
    }

    if (unreadCount > prevCount.current) {
      const latest = messages.find((m) => !m.read) || messages[0];
      if (latest) {
        try {
          new Notification(latest.title || 'ZANUPF', {
            body: latest.body,
            icon: `${import.meta.env.BASE_URL}icon-192.png`,
            tag: `portal-${latest.id}`,
          });
        } catch {
          // ignore
        }
      }
    }
    prevCount.current = unreadCount;
  }, [unreadCount, messages]);
}
