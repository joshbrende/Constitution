import { useCallback, useEffect, useRef, useState } from 'react';
import { useLocation, useNavigate, useParams } from 'react-router-dom';
import {
  blockUser,
  getMessages,
  reportMessage,
  reportThread,
  sendMessage,
} from '../../api/dialogueApi';
import {
  displayAuthor,
  latestSyncTimestamp,
  mergeMessages,
  useDialogueFullRefresh,
  useDialoguePoll,
  useDialogueThreadSubscription,
} from '../../lib/dialogueRealtime';
import { catchMessage } from '../../lib/apiErrors';
import { sanitizeDialogueMessage } from '../../lib/sanitize';
import { dialogueMessageSchema, validateForm } from '../../lib/validation';
import { usePortalNotifications } from '../../context/PortalNotificationsContext';
import { useDialogueAccess } from '../../lib/useDialogueAccess';
import ChatComposer from '../../components/ChatComposer';
import { absoluteMediaUrl } from '../../api/client';
import WorkflowIcon from '../../ui/icons/WorkflowIcon';
import { WORKFLOW_ICON_KEYS } from '../../ui/icons/workflowIcons';

export default function ChatThreadPage() {
  const { threadId } = useParams();
  const { state } = useLocation();
  const navigate = useNavigate();
  const { allowed } = useDialogueAccess();
  const thread = state?.thread;
  const channel = state?.channel;
  const [messages, setMessages] = useState([]);
  const [loading, setLoading] = useState(true);
  const [sending, setSending] = useState(false);
  const [error, setError] = useState(null);
  const [body, setBody] = useState('');
  const bottomRef = useRef(null);
  const messagesRef = useRef([]);
  const isLocked = thread?.status === 'locked';
  const { refresh: refreshNotificationBadge } = usePortalNotifications();

  messagesRef.current = messages;

  const scrollToEnd = useCallback(() => {
    bottomRef.current?.scrollIntoView({ behavior: 'smooth' });
  }, []);

  const loadFull = useCallback(async () => {
    if (!allowed || !threadId) return;
    setError(null);
    try {
      const data = await getMessages(threadId);
      setMessages(Array.isArray(data) ? data : []);
    } catch (e) {
      setError(catchMessage(e, 'Failed to load messages.'));
    } finally {
      setLoading(false);
      refreshNotificationBadge().catch(() => {});
    }
  }, [allowed, threadId, refreshNotificationBadge]);

  const pollIncremental = useCallback(async () => {
    if (!allowed || !threadId) return;
    const since = latestSyncTimestamp(messagesRef.current);
    try {
      if (!since) {
        await loadFull();
        return;
      }
      const incoming = await getMessages(threadId, { since });
      if (Array.isArray(incoming) && incoming.length > 0) {
        setMessages((prev) => mergeMessages(prev, incoming));
        scrollToEnd();
      }
    } catch {
      // keep existing
    }
  }, [allowed, threadId, loadFull, scrollToEnd]);

  const handleLiveMessage = useCallback(
    (message) => {
      setMessages((prev) => mergeMessages(prev, [message]));
      scrollToEnd();
    },
    [scrollToEnd]
  );

  const liveConnected = useDialogueThreadSubscription(allowed ? threadId : null, handleLiveMessage);

  useDialoguePoll(pollIncremental, liveConnected ? 15000 : 2500, allowed);
  useDialogueFullRefresh(loadFull, liveConnected ? 60000 : 12000, allowed);

  useEffect(() => {
    if (!allowed) return;
    loadFull();
  }, [allowed, loadFull]);

  useEffect(() => {
    scrollToEnd();
  }, [messages.length, scrollToEnd]);

  if (!allowed) return null;

  async function handleSend() {
    if (!threadId || isLocked) return;
    const text = sanitizeDialogueMessage(body);
    const validation = validateForm(dialogueMessageSchema, { body: text });
    if (!validation.success) {
      setError(validation.firstError);
      return;
    }
    setSending(true);
    setError(null);
    try {
      const msg = await sendMessage(threadId, validation.data.body);
      setBody('');
      setMessages((prev) => mergeMessages(prev, [msg]));
      scrollToEnd();
    } catch (e) {
      setError(catchMessage(e, 'Could not send message.'));
    } finally {
      setSending(false);
    }
  }

  function handleReportThread() {
    const reason = window.prompt('Report thread — enter reason: spam, harassment, hate, misinformation, other');
    if (!reason || !threadId) return;
    reportThread(threadId, { reason, details: null })
      .then(() => window.alert('Thank you. Our moderators will review this thread.'))
      .catch((e) => window.alert(catchMessage(e, 'Could not report.')));
  }

  function handleMessageMenu(item) {
    if (item.is_deleted) return;
    const action = window.prompt('Type: report or block');
    if (action === 'report') {
      const reason = window.prompt('Reason: spam, harassment, hate, misinformation, other') || 'other';
      reportMessage(item.id, { reason, details: null }).catch((e) =>
        window.alert(catchMessage(e, 'Could not report.'))
      );
    } else if (action === 'block' && item.user?.id) {
      if (window.confirm(`Block ${displayAuthor(item.user)}?`)) {
        blockUser(item.user.id)
          .then(() => setMessages((prev) => prev.filter((m) => m?.user?.id !== item.user.id)))
          .catch((e) => window.alert(catchMessage(e, 'Could not block.')));
      }
    }
  }

  return (
    <div className="flex min-h-[calc(100dvh-7rem)] flex-col">
      <div className="flex items-center justify-between border-b border-app-border px-3 py-2">
        <p className="truncate text-sm font-semibold">{thread?.title || channel?.name || 'Chat'}</p>
        <div className="flex items-center gap-2">
          {liveConnected ? (
            <span className="flex items-center gap-1 text-[10px] text-green-400">
              <WorkflowIcon iconKey={WORKFLOW_ICON_KEYS.dialogueLive} variant="success" size={12} /> Live
            </span>
          ) : (
            <span className="flex items-center gap-1 text-[10px] text-app-muted">
              <WorkflowIcon iconKey="system.networkOffline" variant="muted" size={12} /> Polling
            </span>
          )}
          <button type="button" onClick={handleReportThread} className="text-xs text-app-subtle underline">
            Report
          </button>
        </div>
      </div>

      {isLocked ? (
        <p className="bg-app-gold/10 px-3 py-2 text-center text-xs text-amber-200">
          This chat is locked. You can read messages but not reply.
        </p>
      ) : null}
      {error ? <p className="px-3 py-1 text-center text-xs text-app-error">{error}</p> : null}

      <div className="flex-1 overflow-y-auto px-3 py-2">
        {loading && !messages.length ? (
          <p className="text-center text-sm text-app-subtle">Loading messages…</p>
        ) : (
          messages.map((item) => {
            if (item.is_deleted) {
              return (
                <p key={item.id} className="mb-2 ml-10 text-xs italic text-app-muted">
                  Message removed by moderator.
                </p>
              );
            }
            const author = displayAuthor(item.user);
            const isOfficial = item.is_official || item.user?.name === 'System';
            return (
              <button
                key={item.id}
                type="button"
                onClick={() => handleMessageMenu(item)}
                className="mb-3 flex w-full gap-2 text-left"
              >
                <div
                  className={`flex h-8 w-8 shrink-0 items-center justify-center rounded-full border text-sm font-bold ${
                    isOfficial ? 'border-app-gold text-app-gold' : 'border-app-border text-app-subtle'
                  }`}
                >
                  {author.charAt(0).toUpperCase()}
                </div>
                <div
                  className={`min-w-0 flex-1 rounded-xl border p-2 ${
                    isOfficial
                      ? 'border-app-gold/40 bg-app-gold/5'
                      : 'border-app-border bg-app-surface'
                  }`}
                >
                  <div className="flex items-center gap-2">
                    <span className={`text-xs font-semibold ${isOfficial ? 'text-amber-200' : ''}`}>
                      {author}
                      {isOfficial ? ' · Editor' : ''}
                    </span>
                    {item.is_pinned ? (
                      <span className="flex items-center gap-0.5 text-[10px] text-app-gold">
                        <WorkflowIcon iconKey={WORKFLOW_ICON_KEYS.dialoguePinned} size={10} /> Pinned
                      </span>
                    ) : null}
                  </div>
                  <p className="mt-1 text-sm">{item.body}</p>
                  {(item.attachments ?? []).map((a) =>
                    a?.url ? (
                      <a
                        key={a.id || a.url}
                        href={absoluteMediaUrl(a.url)}
                        target="_blank"
                        rel="noopener noreferrer"
                        onClick={(e) => e.stopPropagation()}
                        className="mt-2 block text-xs text-app-gold underline"
                      >
                        {a.name || 'Attachment'}
                      </a>
                    ) : null
                  )}
                  {item.created_at ? (
                    <p className="mt-1 text-[10px] text-app-muted">
                      {new Date(item.created_at).toLocaleTimeString()}
                    </p>
                  ) : null}
                </div>
              </button>
            );
          })
        )}
        <div ref={bottomRef} />
      </div>

      {!isLocked ? (
        <ChatComposer
          value={body}
          onChange={setBody}
          onSubmit={handleSend}
          placeholder="Comment on this chat…"
          submitting={sending}
          disabled={sending}
        />
      ) : null}
    </div>
  );
}
