import { NavLink, useNavigate } from 'react-router-dom';
import { useMenu } from '../context/MenuContext';
import { useGuest } from '../context/GuestContext';
import { useAppConfig } from '../context/AppConfigContext';
import WorkflowIcon from '../ui/icons/WorkflowIcon';
import { WORKFLOW_ICON_KEYS } from '../ui/icons/workflowIcons';

const MAIN_ITEMS = [
  { to: '/home', label: 'Home', iconKey: WORKFLOW_ICON_KEYS.homeTabHome },
  { to: '/constitutions', label: 'Constitutions', iconKey: WORKFLOW_ICON_KEYS.homeConstitution },
  { to: '/constitutions/bookmarks', label: 'Bookmarks', iconKey: 'reader.bookmark' },
  { to: '/constitutions/highlights', label: 'Highlights', iconKey: 'reader.highlight' },
  { to: '/chat', label: 'Chat', iconKey: WORKFLOW_ICON_KEYS.homeChat, authOnly: true, dialogueOnly: true },
  { to: '/profile', label: 'Profile', iconKey: WORKFLOW_ICON_KEYS.homeTabProfile, authOnly: true },
];

const EXTRA_ITEMS = [
  { slug: 'help', label: 'Help', iconKey: 'system.alert' },
  { slug: 'settings', label: 'Settings', iconKey: 'reader.settings' },
  { to: '/about', label: 'About', iconKey: WORKFLOW_ICON_KEYS.homeParty },
  { slug: 'privacy', label: 'Privacy', iconKey: 'system.forbidden' },
  { slug: 'terms', label: 'Terms', iconKey: 'library.document' },
  { slug: 'cookies', label: 'Cookies', iconKey: 'library.document' },
];

export default function SideMenu() {
  const { menuOpen, closeMenu } = useMenu();
  const { isGuest } = useGuest();
  const { dialogueEnabled } = useAppConfig();
  const navigate = useNavigate();

  if (!menuOpen) return null;

  function goStatic(slug, label) {
    closeMenu();
    navigate(`/pages/${slug}`, { state: { title: label } });
  }

  return (
    <div className="fixed inset-0 z-50 flex">
      <button
        type="button"
        aria-label="Close menu"
        className="flex-1 bg-black/60"
        onClick={closeMenu}
      />
      <aside className="flex h-full w-[min(280px,85vw)] flex-col bg-app-surface shadow-xl">
        <div className="flex items-center justify-between border-b border-app-border px-4 py-3">
          <span className="font-semibold text-app-text">Menu</span>
          <button type="button" onClick={closeMenu} className="rounded-lg p-1 text-app-subtle">
            <WorkflowIcon iconKey={WORKFLOW_ICON_KEYS.readerClose} variant="muted" size={22} />
          </button>
        </div>
        <nav className="flex-1 overflow-y-auto p-2">
          {MAIN_ITEMS.filter(
            (i) => (!i.authOnly || !isGuest) && (!i.dialogueOnly || dialogueEnabled)
          ).map((item) => (
            <NavLink
              key={item.to}
              to={item.to}
              onClick={closeMenu}
              className={({ isActive }) =>
                `flex items-center gap-3 rounded-lg px-3 py-3 text-sm ${
                  isActive ? 'bg-app-bg text-app-gold' : 'text-app-text hover:bg-app-bg'
                }`
              }
            >
              {({ isActive }) => (
                <>
                  <WorkflowIcon
                    iconKey={item.iconKey}
                    size={18}
                    variant={isActive ? 'gold' : 'muted'}
                  />
                  <span>{item.label}</span>
                </>
              )}
            </NavLink>
          ))}
          <hr className="my-2 border-app-border" />
          {EXTRA_ITEMS.map((item) =>
            item.to ? (
              <NavLink
                key={item.to}
                to={item.to}
                onClick={closeMenu}
                className="flex items-center gap-3 rounded-lg px-3 py-3 text-sm text-app-text hover:bg-app-bg"
              >
                <WorkflowIcon iconKey={item.iconKey} size={18} variant="muted" />
                <span>{item.label}</span>
              </NavLink>
            ) : (
              <button
                key={item.slug}
                type="button"
                onClick={() => goStatic(item.slug, item.label)}
                className="flex w-full items-center gap-3 rounded-lg px-3 py-3 text-left text-sm text-app-text hover:bg-app-bg"
              >
                <WorkflowIcon iconKey={item.iconKey} size={18} variant="muted" />
                <span>{item.label}</span>
              </button>
            )
          )}
        </nav>
      </aside>
    </div>
  );
}
