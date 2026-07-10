import { useNavigate } from 'react-router-dom';
import { useGuest } from '../context/GuestContext';
import { setAuthToken } from '../api/client';

export default function GuestBanner() {
  const { isGuest, exitGuestMode } = useGuest();
  const navigate = useNavigate();

  if (!isGuest) return null;

  function goSignIn() {
    exitGuestMode();
    setAuthToken(null);
    navigate('/login', { replace: true });
  }

  return (
    <div className="flex items-center justify-between gap-2 bg-app-surface border-b border-app-border px-3 py-2">
      <span className="text-xs text-app-subtle">Browsing as guest — constitutions only</span>
      <button
        type="button"
        onClick={goSignIn}
        className="shrink-0 rounded-full bg-app-green px-3 py-1 text-xs font-semibold text-white"
      >
        Sign in
      </button>
    </div>
  );
}
