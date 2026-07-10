import { useNavigate } from 'react-router-dom';
import { useMenu } from '../context/MenuContext';
import { usePortalNotifications } from '../context/PortalNotificationsContext';
import { useGuest } from '../context/GuestContext';
import WorkflowIcon from '../ui/icons/WorkflowIcon';
import { WORKFLOW_ICON_KEYS } from '../ui/icons/workflowIcons';

export default function AppHeader({ title, showBack = false, showMenu = true, showBell = true }) {
  const navigate = useNavigate();
  const { openMenu } = useMenu();
  const { unreadCount } = usePortalNotifications();
  const { isGuest } = useGuest();

  function onBellClick() {
    if (isGuest) {
      navigate('/login');
      return;
    }
    navigate('/home/notifications');
  }

  return (
    <header className="sticky top-0 z-30 flex h-12 items-center justify-between border-b border-app-border bg-app-bg px-3">
      <div className="flex w-10 items-center justify-start">
        {showBack ? (
          <button
            type="button"
            aria-label="Go back"
            onClick={() => navigate(-1)}
            className="rounded-lg p-2 text-app-text hover:bg-app-surface"
          >
            <WorkflowIcon iconKey={WORKFLOW_ICON_KEYS.navBack} variant="current" size={20} />
          </button>
        ) : showMenu ? (
          <button
            type="button"
            aria-label="Open menu"
            onClick={openMenu}
            className="rounded-lg p-2 text-app-text hover:bg-app-surface"
          >
            <WorkflowIcon iconKey={WORKFLOW_ICON_KEYS.navMenu} variant="current" size={20} />
          </button>
        ) : (
          <span className="w-9" />
        )}
      </div>
      <h1 className="flex-1 truncate text-center text-sm font-semibold text-app-text">{title}</h1>
      <div className="flex w-10 items-center justify-end">
        {showBell ? (
          <button
            type="button"
            aria-label="Notifications"
            onClick={onBellClick}
            className="relative rounded-lg p-2 text-app-text hover:bg-app-surface"
          >
            <WorkflowIcon iconKey={WORKFLOW_ICON_KEYS.systemBell} variant="current" size={20} />
            {!isGuest && unreadCount > 0 ? (
              <span className="absolute right-1 top-1 flex h-4 min-w-4 items-center justify-center rounded-full bg-app-gold px-1 text-[10px] font-bold text-app-bg">
                {unreadCount > 99 ? '99+' : unreadCount}
              </span>
            ) : null}
          </button>
        ) : (
          <span className="w-9" />
        )}
      </div>
    </header>
  );
}
