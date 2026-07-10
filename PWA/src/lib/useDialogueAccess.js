import { useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import { useAppConfig } from '../context/AppConfigContext';
import { useGuest } from '../context/GuestContext';

/** Redirect guests and block chat when dialogue is disabled in app-config. */
export function useDialogueAccess() {
  const navigate = useNavigate();
  const { isGuest } = useGuest();
  const { dialogueEnabled, loading } = useAppConfig();

  useEffect(() => {
    if (loading) return;
    if (!dialogueEnabled) {
      navigate('/home', { replace: true });
      return;
    }
    if (isGuest) {
      navigate('/login', { replace: true });
    }
  }, [dialogueEnabled, isGuest, loading, navigate]);

  return {
    loading,
    dialogueEnabled,
    allowed: !loading && dialogueEnabled && !isGuest,
  };
}
