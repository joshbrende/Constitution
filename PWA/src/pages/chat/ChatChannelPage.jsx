import { useCallback, useState } from 'react';
import { useLocation, useNavigate, useParams } from 'react-router-dom';
import { getThreads } from '../../api/dialogueApi';
import { displayAuthor, useDialoguePoll } from '../../lib/dialogueRealtime';
import { catchMessage } from '../../lib/apiErrors';
import { useDialogueAccess } from '../../lib/useDialogueAccess';
import ScaleButton from '../../components/ScaleButton';
import WorkflowIcon from '../../ui/icons/WorkflowIcon';
import { WORKFLOW_ICON_KEYS } from '../../ui/icons/workflowIcons';

export default function ChatChannelPage() {
  const { channelId } = useParams();
  const { state } = useLocation();
  const channel = state?.channel;
  const navigate = useNavigate();
  const { allowed } = useDialogueAccess();
  const [threads, setThreads] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  const load = useCallback(async () => {
    if (!allowed || !channelId) return;
    setError(null);
    try {
      const data = await getThreads(channelId);
      setThreads(Array.isArray(data) ? data : []);
    } catch (e) {
      setError(catchMessage(e, 'Failed to load chats.'));
      setThreads([]);
    } finally {
      setLoading(false);
    }
  }, [allowed, channelId]);

  useDialoguePoll(load, 4000, allowed);

  if (!allowed) return null;
  if (loading && !threads.length) return <p className="p-4 text-sm text-app-subtle">Loading…</p>;

  return (
    <div className="flex min-h-full flex-col pb-4">
      <div className="border-b border-app-border bg-app-gold/5 px-4 py-3 text-xs text-app-subtle">
        Editors start each chat. Open a thread below to read and join the conversation.
      </div>
      {error ? <p className="p-3 text-sm text-app-error">{error}</p> : null}

      <div className="space-y-2 p-4">
        {threads.length === 0 ? (
          <p className="text-center text-sm text-app-muted">
            No chats yet. An editor will open a conversation here when a topic is ready.
          </p>
        ) : (
          threads.map((thread) => {
            const author = displayAuthor(thread.creator);
            const locked = thread.status === 'locked';
            return (
              <ScaleButton
                key={thread.id}
                onClick={() =>
                  navigate(`/chat/threads/${thread.id}`, {
                    state: { channel: channel || { id: channelId, name: 'Channel' }, thread },
                  })
                }
                className={`flex w-full items-center gap-3 rounded-xl border bg-app-surface p-4 text-left ${
                  locked ? 'border-app-muted/40 opacity-80' : 'border-app-border'
                }`}
              >
                <div className="min-w-0 flex-1">
                  <p className="font-semibold">{thread.title}</p>
                  <p className="text-xs text-app-subtle">
                    Started by {author}
                    {thread.messages_count != null
                      ? ` · ${thread.messages_count} message${thread.messages_count === 1 ? '' : 's'}`
                      : ''}
                    {locked ? ' · Locked' : ''}
                  </p>
                  {thread.last_message_at ? (
                    <p className="mt-1 text-[11px] text-app-muted">
                      Last activity {new Date(thread.last_message_at).toLocaleString()}
                    </p>
                  ) : null}
                </div>
                {locked ? (
                  <WorkflowIcon iconKey="system.forbidden" variant="muted" size={16} />
                ) : (
                  <WorkflowIcon iconKey={WORKFLOW_ICON_KEYS.navChevronForward} variant="muted" size={18} />
                )}
              </ScaleButton>
            );
          })
        )}
      </div>
    </div>
  );
}
