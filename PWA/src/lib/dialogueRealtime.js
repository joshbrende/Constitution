import { useCallback, useEffect, useRef, useState } from 'react';
import { subscribeDialogueThread } from './dialoguePusher';

/** Poll while component is mounted (web equivalent of mobile useDialoguePoll). */
export function useDialoguePoll(callback, intervalMs = 3000, enabled = true) {
  const callbackRef = useRef(callback);
  callbackRef.current = callback;

  useEffect(() => {
    if (!enabled) return undefined;
    let cancelled = false;

    const tick = async () => {
      if (cancelled) return;
      try {
        await callbackRef.current(false);
      } catch {
        // caller handles
      }
    };

    tick();
    const id = setInterval(tick, intervalMs);
    return () => {
      cancelled = true;
      clearInterval(id);
    };
  }, [intervalMs, enabled]);
}

export function useDialogueFullRefresh(callback, intervalMs = 12000, enabled = true) {
  const callbackRef = useRef(callback);
  callbackRef.current = callback;

  useEffect(() => {
    if (!enabled) return undefined;
    const id = setInterval(() => {
      callbackRef.current(true).catch(() => {});
    }, intervalMs);
    return () => clearInterval(id);
  }, [intervalMs, enabled]);
}

export function latestSyncTimestamp(messages) {
  if (!Array.isArray(messages) || messages.length === 0) return null;
  return messages.reduce((max, message) => {
    const stamp = message?.updated_at || message?.created_at;
    if (!stamp) return max;
    if (!max || new Date(stamp).getTime() > new Date(max).getTime()) return stamp;
    return max;
  }, null);
}

export function mergeMessages(existing, incoming) {
  const byId = new Map(existing.map((m) => [m.id, m]));
  for (const message of incoming) byId.set(message.id, message);
  return [...byId.values()].sort(
    (a, b) => new Date(a.created_at).getTime() - new Date(b.created_at).getTime()
  );
}

export function displayAuthor(user) {
  const rawName = user?.name || 'Member';
  return rawName === 'System' ? 'Editor' : rawName;
}

export function useDialogueThreadSubscription(threadId, onMessage) {
  const onMessageRef = useRef(onMessage);
  onMessageRef.current = onMessage;
  const [live, setLive] = useState(false);

  useEffect(() => {
    if (!threadId) {
      setLive(false);
      return undefined;
    }

    let cancelled = false;
    let subscription = null;

    (async () => {
      try {
        const handle = await subscribeDialogueThread(threadId, (message) => {
          if (!cancelled) onMessageRef.current(message);
        });
        if (cancelled) {
          handle?.unsubscribe?.();
          return;
        }
        subscription = handle;
        setLive(!!handle);
      } catch {
        if (!cancelled) setLive(false);
      }
    })();

    return () => {
      cancelled = true;
      setLive(false);
      subscription?.unsubscribe?.();
    };
  }, [threadId]);

  return live;
}
