import { useCallback, useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { getChannels } from '../../api/dialogueApi';
import { useDialoguePoll } from '../../lib/dialogueRealtime';
import { catchMessage } from '../../lib/apiErrors';
import { useDialogueAccess } from '../../lib/useDialogueAccess';
import ScaleButton from '../../components/ScaleButton';
import WorkflowIcon from '../../ui/icons/WorkflowIcon';
import { WORKFLOW_ICON_KEYS } from '../../ui/icons/workflowIcons';

export default function ChatHomePage() {
  const navigate = useNavigate();
  const { allowed } = useDialogueAccess();
  const [channels, setChannels] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  const load = useCallback(async () => {
    if (!allowed) return;
    setError(null);
    try {
      const data = await getChannels();
      setChannels(Array.isArray(data) ? data : []);
    } catch (e) {
      setError(catchMessage(e, 'Failed to load dialogue channels.'));
      setChannels([]);
    } finally {
      setLoading(false);
    }
  }, [allowed]);

  useDialoguePoll(load, 5000, allowed);

  if (!allowed) return null;
  if (loading) return <p className="p-4 text-sm text-app-subtle">Loading…</p>;

  return (
    <div className="space-y-4 p-4 pb-8">
      <div>
        <h2 className="text-xl font-bold">Chat</h2>
        <p className="text-sm text-app-subtle">
          Structured dialogue with the Presidium and Leagues, anchored to the ZANU PF and Zimbabwe
          Constitutions.
        </p>
      </div>

      {error ? (
        <div className="rounded-lg border border-red-900/50 bg-red-950/20 p-3 text-sm">
          {error}
          <button type="button" onClick={load} className="ml-2 text-app-gold">
            Retry
          </button>
        </div>
      ) : null}

      {channels.map((ch) => {
        const unread = ch.unread_count || 0;
        const hasOfficial = !!ch.has_official_reply;
        return (
          <ScaleButton
            key={ch.id}
            onClick={() => navigate(`/chat/channels/${ch.id}`, { state: { channel: ch } })}
            className="flex w-full gap-3 rounded-xl border border-app-border bg-app-surface p-4 text-left"
          >
            <div
              className={`flex h-12 w-12 shrink-0 items-center justify-center rounded-xl border ${
                hasOfficial ? 'border-red-500/50 bg-red-950/30' : 'border-app-gold/30 bg-app-gold/10'
              }`}
            >
              <WorkflowIcon
                iconKey={
                  hasOfficial
                    ? WORKFLOW_ICON_KEYS.dialogueChannelOfficial
                    : WORKFLOW_ICON_KEYS.dialogueChannel
                }
                variant={hasOfficial ? 'danger' : 'gold'}
                size={26}
              />
            </div>
            <div className="min-w-0 flex-1">
              <div className="flex items-center gap-2">
                <p className="font-semibold">{ch.name}</p>
                {unread > 0 ? (
                  <span className="rounded-full bg-app-gold px-2 py-0.5 text-[10px] font-bold text-app-bg">
                    {unread > 999 ? '999+' : unread}
                  </span>
                ) : null}
              </div>
              {ch.description ? (
                <p className="line-clamp-2 text-xs text-app-subtle">{ch.description}</p>
              ) : null}
            </div>
          </ScaleButton>
        );
      })}
    </div>
  );
}
