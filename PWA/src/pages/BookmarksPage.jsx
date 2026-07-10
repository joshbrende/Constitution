import { useNavigate } from 'react-router-dom';
import { useReaderData } from '../context/ReaderDataContext';
import WorkflowIcon from '../ui/icons/WorkflowIcon';
import { WORKFLOW_ICON_KEYS } from '../ui/icons/workflowIcons';

const SECTION_LABELS = { zanupf: 'Article', zimbabwe: 'Section' };

export default function BookmarksPage() {
  const navigate = useNavigate();
  const { getBookmarksList } = useReaderData();
  const list = getBookmarksList();
  const labelFor = (item) =>
    item.logical_number || SECTION_LABELS[item.constitution_slug] || 'Article';

  if (list.length === 0) {
    return (
      <div className="flex min-h-[50vh] flex-col items-center justify-center p-8 text-center">
        <WorkflowIcon iconKey={WORKFLOW_ICON_KEYS.bookmarksEmpty} variant="muted" size={48} />
        <h2 className="mt-4 text-lg font-bold text-app-text">No bookmarks yet</h2>
        <p className="mt-2 text-sm text-app-subtle">
          Tap the bookmark icon while reading to save sections.
        </p>
      </div>
    );
  }

  return (
    <ul className="space-y-2 p-4">
      {list.map((item) => (
        <li key={item.id}>
          <button
            type="button"
            onClick={() =>
              navigate(
                `/constitutions/sections/${item.id}?doc=${item.constitution_slug || 'zanupf'}`
              )
            }
            className="flex w-full items-center gap-3 rounded-xl border border-app-border bg-app-surface p-4 text-left"
          >
            <WorkflowIcon iconKey={WORKFLOW_ICON_KEYS.readerBookmark} size={20} />
            <div className="min-w-0 flex-1">
              <p className="text-xs text-app-subtle">{labelFor(item)}</p>
              <p className="truncate font-semibold text-app-text">{item.title}</p>
            </div>
            <WorkflowIcon iconKey={WORKFLOW_ICON_KEYS.navChevronForward} variant="muted" size={18} />
          </button>
        </li>
      ))}
    </ul>
  );
}
