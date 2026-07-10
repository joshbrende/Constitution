import { NavLink } from 'react-router-dom';
import { useGuest } from '../context/GuestContext';
import { useAppConfig } from '../context/AppConfigContext';
import WorkflowIcon from '../ui/icons/WorkflowIcon';
import { WORKFLOW_ICON_KEYS } from '../ui/icons/workflowIcons';

const TABS = [
  { to: '/home', label: 'Home', iconKey: WORKFLOW_ICON_KEYS.homeTabHome, end: true },
  {
    to: '/constitutions',
    label: 'Constitutions',
    iconKey: WORKFLOW_ICON_KEYS.homeTabConstitution,
  },
  {
    to: '/chat',
    label: 'Chat',
    iconKey: WORKFLOW_ICON_KEYS.homeTabChat,
    authOnly: true,
    dialogueOnly: true,
  },
  { to: '/profile', label: 'Profile', iconKey: WORKFLOW_ICON_KEYS.homeTabProfile, authOnly: true },
];

export default function BottomTabs() {
  const { isGuest } = useGuest();
  const { dialogueEnabled } = useAppConfig();

  const visible = TABS.filter(
    (t) => (!t.authOnly || !isGuest) && (!t.dialogueOnly || dialogueEnabled)
  );

  return (
    <nav className="fixed bottom-0 left-1/2 z-40 flex h-16 w-full max-w-[428px] -translate-x-1/2 border-t border-app-border bg-app-surface pb-[env(safe-area-inset-bottom)]">
      {visible.map(({ to, label, iconKey, end }) => (
        <NavLink
          key={to}
          to={to}
          end={end}
          className={({ isActive }) =>
            `flex flex-1 flex-col items-center justify-center gap-0.5 text-[11px] ${
              isActive ? 'text-app-gold' : 'text-app-muted'
            }`
          }
        >
          {({ isActive }) => (
            <>
              <WorkflowIcon
                iconKey={iconKey}
                size={22}
                variant={isActive ? 'gold' : 'muted'}
              />
              <span>{label}</span>
            </>
          )}
        </NavLink>
      ))}
    </nav>
  );
}
