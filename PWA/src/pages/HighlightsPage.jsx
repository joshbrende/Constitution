import { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { useReaderData } from '../context/ReaderDataContext';
import WorkflowIcon from '../ui/icons/WorkflowIcon';
import { WORKFLOW_ICON_KEYS } from '../ui/icons/workflowIcons';

const SECTION_LABELS = { zanupf: 'Article', zimbabwe: 'Section' };

export default function HighlightsPage() {
  const navigate = useNavigate();
  const { getAllTextHighlights, removeTextHighlight, updateTextHighlightNote } = useReaderData();
  const list = getAllTextHighlights();
  const [editItem, setEditItem] = useState(null);
  const [editNoteInput, setEditNoteInput] = useState('');

  function openEdit(item) {
    setEditItem(item);
    setEditNoteInput(item.note || '');
  }

  function saveEdit() {
    if (!editItem) return;
    updateTextHighlightNote(editItem.sectionId, editItem.id, editNoteInput);
    setEditItem(null);
    setEditNoteInput('');
  }

  function handleRemove(item) {
    if (!window.confirm('Remove this highlight and any note?')) return;
    removeTextHighlight(item.sectionId, item.id);
  }

  if (list.length === 0) {
    return (
      <div className="flex min-h-[50vh] flex-col items-center justify-center p-8 text-center">
        <WorkflowIcon iconKey={WORKFLOW_ICON_KEYS.highlightsEmpty} variant="muted" size={48} />
        <h2 className="mt-4 text-lg font-bold text-app-text">No highlights yet</h2>
        <p className="mt-2 text-sm text-app-subtle">
          Select text while reading and tap Highlight or Add note to save passages.
        </p>
      </div>
    );
  }

  return (
    <>
      <ul className="space-y-2 p-4">
        {list.map((item) => (
          <li key={`${item.sectionId}-${item.id}`}>
            <div className="flex items-stretch gap-0 overflow-hidden rounded-xl border border-app-border bg-app-surface">
              <div className="w-1 shrink-0 bg-app-gold/60" />
              <button
                type="button"
                onClick={() =>
                  navigate(
                    `/constitutions/sections/${item.sectionId}?doc=${item.constitution_slug || 'zanupf'}`
                  )
                }
                className="min-w-0 flex-1 p-4 text-left"
              >
                <p className="truncate text-xs text-app-subtle">
                  {item.sectionLogicalNumber ||
                    SECTION_LABELS[item.constitution_slug] ||
                    'Article'}{' '}
                  {item.sectionTitle || ''}
                </p>
                <p className="mt-1 line-clamp-2 text-sm italic text-app-text">"{item.text}"</p>
                {item.note ? (
                  <p className="mt-1 line-clamp-2 text-xs text-app-muted">{item.note}</p>
                ) : null}
              </button>
              <div className="flex shrink-0 flex-col justify-center gap-1 pr-2">
                <button
                  type="button"
                  aria-label="Edit note"
                  onClick={() => openEdit(item)}
                  className="rounded-lg p-2 text-app-subtle hover:bg-app-bg"
                >
                  <WorkflowIcon iconKey={WORKFLOW_ICON_KEYS.actionEdit} variant="muted" size={16} />
                </button>
                <button
                  type="button"
                  aria-label="Open section"
                  onClick={() =>
                    navigate(
                      `/constitutions/sections/${item.sectionId}?doc=${item.constitution_slug || 'zanupf'}`
                    )
                  }
                  className="rounded-lg p-2 text-app-subtle hover:bg-app-bg"
                >
                  <WorkflowIcon iconKey={WORKFLOW_ICON_KEYS.navChevronForward} variant="muted" size={16} />
                </button>
              </div>
            </div>
            <button
              type="button"
              onClick={() => handleRemove(item)}
              className="mt-1 text-xs text-app-error underline"
            >
              Remove
            </button>
          </li>
        ))}
      </ul>

      {editItem ? (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-6">
          <div className="w-full max-w-md rounded-2xl border border-app-border bg-app-surface p-5">
            <h2 className="text-lg font-bold text-app-text">Edit note</h2>
            <p className="mt-2 line-clamp-2 text-sm italic text-app-subtle">"{editItem.text}"</p>
            <textarea
              value={editNoteInput}
              onChange={(e) => setEditNoteInput(e.target.value)}
              placeholder="Type your note…"
              rows={4}
              className="mt-3 w-full rounded-lg border border-app-border bg-app-bg px-3 py-2 text-sm text-app-text"
              autoFocus
            />
            <div className="mt-4 flex justify-end gap-2">
              <button
                type="button"
                onClick={() => {
                  setEditItem(null);
                  setEditNoteInput('');
                }}
                className="rounded-full bg-app-bg px-4 py-2 text-sm font-semibold text-app-text"
              >
                Cancel
              </button>
              <button
                type="button"
                onClick={saveEdit}
                className="rounded-full bg-app-gold px-4 py-2 text-sm font-bold text-black"
              >
                Save
              </button>
            </div>
          </div>
        </div>
      ) : null}
    </>
  );
}
