import { useCallback, useEffect, useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { usePortalNotifications } from '../context/PortalNotificationsContext';
import { openNotificationTarget } from '../lib/notificationNavigation';
import ScaleButton from '../components/ScaleButton';
import WorkflowIcon from '../ui/icons/WorkflowIcon';
import { WORKFLOW_ICON_KEYS } from '../ui/icons/workflowIcons';

/** Ported from mobile/src/screens/NotificationsScreen.js */
export default function NotificationsPage() {
  const navigate = useNavigate();
  const { messages, unreadCount, refresh, markRead, markAllRead } = usePortalNotifications();
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  const load = useCallback(async () => {
    try {
      setError(null);
      await refresh();
    } catch (e) {
      setError(e?.userMessage || 'Failed to load notifications.');
    } finally {
      setLoading(false);
    }
  }, [refresh]);

  useEffect(() => {
    load();
  }, [load]);

  async function handleMessagePress(msg) {
    if (!msg.read && msg.id) {
      try {
        await markRead(msg.id);
      } catch {
        // continue navigation
      }
    }
    openNotificationTarget(navigate, msg);
  }

  if (loading) return <p className="p-4 text-sm text-app-subtle">Loading…</p>;
  if (error && messages.length === 0) {
    return (
      <div className="p-4 text-center">
        <p className="text-sm text-app-error">{error}</p>
        <button type="button" onClick={load} className="mt-2 text-app-gold">
          Try again
        </button>
      </div>
    );
  }

  return (
    <div className="space-y-4 p-4 pb-8">
      <div className="flex items-center justify-between gap-2">
        <div>
          <h2 className="text-xl font-bold">Notifications</h2>
          <p className="text-sm text-app-subtle">
            Updates from editors, administrators, and your academy certificate application.
          </p>
        </div>
        {unreadCount > 0 && (
          <button type="button" onClick={() => markAllRead()} className="shrink-0 text-xs font-semibold text-app-gold">
            Mark all read
          </button>
        )}
      </div>

      {messages.length === 0 ? (
        <div className="rounded-xl border border-app-border bg-app-surface p-8 text-center">
          <WorkflowIcon iconKey={WORKFLOW_ICON_KEYS.systemBell} variant="muted" size={48} className="mx-auto" />
          <p className="mt-3 font-semibold">No notifications yet</p>
          <p className="mt-1 text-sm text-app-subtle">
            Announcements, academy updates, and chat alerts will appear here.
          </p>
        </div>
      ) : (
        messages.map((msg) => {
          const hasAction =
            msg.application_id ||
            msg.cta_type === 'external' ||
            (msg.cta_type === 'internal' && (msg.cta_screen || msg.cta_tab));

          return (
            <ScaleButton
              key={msg.id}
              onClick={() => handleMessagePress(msg)}
              disabled={!hasAction && msg.read}
              className={`block w-full rounded-xl border p-4 text-left ${
                !msg.read ? 'border-app-gold bg-app-surface' : 'border-app-border bg-app-surface/80'
              }`}
            >
              <div className="flex items-start justify-between gap-2">
                <p className="font-bold">{msg.title}</p>
                {!msg.read && <span className="mt-1 h-2 w-2 shrink-0 rounded-full bg-app-gold" />}
              </div>
              <p className="mt-2 text-sm text-app-subtle">{msg.body}</p>
              {msg.receipt_number ? (
                <p className="mt-2 font-mono text-xs text-app-gold">Receipt {msg.receipt_number}</p>
              ) : null}
              {msg.cta_label && hasAction ? (
                <p className="mt-2 text-sm font-semibold text-app-gold">{msg.cta_label} →</p>
              ) : null}
              {msg.at ? (
                <p className="mt-1 text-xs text-app-muted">{new Date(msg.at).toLocaleString()}</p>
              ) : null}
            </ScaleButton>
          );
        })
      )}
    </div>
  );
}
