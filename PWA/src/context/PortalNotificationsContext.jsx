import { createContext, useCallback, useContext, useEffect, useMemo, useState } from 'react';
import {
  getPortalNotifications,
  markAllPortalNotificationsRead,
  markPortalNotificationRead,
} from '../api/notificationsApi';
import { getAuthToken } from '../api/authStorage';
import { setAuthToken } from '../api/client';
import { useGuest } from './GuestContext';

const PortalNotificationsContext = createContext({
  unreadCount: 0,
  messages: [],
  loading: false,
  refresh: async () => {},
  markRead: async () => {},
  markAllRead: async () => {},
});

/** Ported from mobile/src/context/PortalNotificationsContext.js */
export function PortalNotificationsProvider({ children }) {
  const { isGuest } = useGuest();
  const [unreadCount, setUnreadCount] = useState(0);
  const [messages, setMessages] = useState([]);
  const [loading, setLoading] = useState(false);

  const applyCounts = useCallback((data) => {
    const portal = Number(data?.unread_portal_messages_count ?? 0);
    const dialogue = Number(data?.unread_dialogue_messages_count ?? 0);
    const total = Number.isFinite(Number(data?.unread_count))
      ? Number(data.unread_count)
      : portal + dialogue;
    setUnreadCount(total);
  }, []);

  const refresh = useCallback(async () => {
    if (isGuest) {
      setUnreadCount(0);
      setMessages([]);
      return;
    }
    const token = await getAuthToken();
    if (!token) {
      setUnreadCount(0);
      setMessages([]);
      return;
    }
    // Ensure axios has the Bearer header before polling (covers late token saves).
    setAuthToken(token);
    try {
      setLoading(true);
      const data = await getPortalNotifications();
      applyCounts(data);
      setMessages(Array.isArray(data?.portal_messages) ? data.portal_messages : []);
    } catch {
      // keep last known
    } finally {
      setLoading(false);
    }
  }, [applyCounts, isGuest]);

  useEffect(() => {
    if (isGuest) return undefined;
    refresh();
    const id = setInterval(refresh, 30000);
    return () => clearInterval(id);
  }, [isGuest, refresh]);

  const markRead = useCallback(async (notificationId) => {
    const result = await markPortalNotificationRead(notificationId);
    applyCounts(result);
    setMessages((prev) =>
      prev.map((msg) => (msg.id === notificationId ? { ...msg, read: true } : msg))
    );
  }, [applyCounts]);

  const markAllRead = useCallback(async () => {
    const result = await markAllPortalNotificationsRead();
    applyCounts(result);
    setMessages((prev) => prev.map((msg) => ({ ...msg, read: true })));
  }, [applyCounts]);

  const value = useMemo(
    () => ({ unreadCount, messages, loading, refresh, markRead, markAllRead }),
    [unreadCount, messages, loading, refresh, markRead, markAllRead]
  );

  return (
    <PortalNotificationsContext.Provider value={value}>{children}</PortalNotificationsContext.Provider>
  );
}

export function usePortalNotifications() {
  return useContext(PortalNotificationsContext);
}
