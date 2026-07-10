import { useMemo, useRef, useState } from 'react';
import { applySuggestion, getShonaSuggestions } from '../lib/shonaAutocomplete';
import WorkflowIcon from '../ui/icons/WorkflowIcon';
import { WORKFLOW_ICON_KEYS } from '../ui/icons/workflowIcons';

export default function ChatComposer({
  value,
  onChange,
  onSubmit,
  placeholder = 'Type your message…',
  submitting = false,
  disabled = false,
  enableShonaSuggestions = true,
}) {
  const inputRef = useRef(null);
  const [focused, setFocused] = useState(false);

  const suggestions = useMemo(() => {
    if (!enableShonaSuggestions || !focused) return [];
    return getShonaSuggestions(value);
  }, [enableShonaSuggestions, focused, value]);

  function pickSuggestion(word) {
    onChange(applySuggestion(value, word));
    inputRef.current?.focus();
  }

  function handleSubmit(e) {
    e?.preventDefault();
    if (!value.trim() || submitting || disabled) return;
    onSubmit();
  }

  return (
    <div className="border-t border-app-border bg-app-bg px-3 py-2 pb-[max(0.5rem,env(safe-area-inset-bottom))]">
      {suggestions.length > 0 ? (
        <div className="mb-2 flex gap-2 overflow-x-auto">
          {suggestions.map((word) => (
            <button
              key={word}
              type="button"
              onClick={() => pickSuggestion(word)}
              className="shrink-0 rounded-full border border-app-border bg-app-surface px-3 py-1 text-xs text-app-gold"
            >
              {word}
            </button>
          ))}
        </div>
      ) : null}
      <form onSubmit={handleSubmit} className="flex items-end gap-2">
        <textarea
          ref={inputRef}
          value={value}
          onChange={(e) => onChange(e.target.value)}
          onFocus={() => setFocused(true)}
          onBlur={() => setFocused(false)}
          placeholder={placeholder}
          rows={2}
          disabled={disabled || submitting}
          className="max-h-32 min-h-[44px] flex-1 resize-none rounded-xl border border-app-border bg-app-surface px-3 py-2 text-sm text-app-text outline-none focus:border-app-gold"
        />
        <button
          type="submit"
          disabled={!value.trim() || submitting || disabled}
          className="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-app-green text-white disabled:opacity-40"
          aria-label="Send message"
        >
          <WorkflowIcon iconKey={WORKFLOW_ICON_KEYS.actionSend} variant="white" size={18} />
        </button>
      </form>
    </div>
  );
}
