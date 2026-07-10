import { createContext, useCallback, useContext, useMemo, useState } from 'react';
import { clearAuthTokens } from '../api/authStorage';
import { setAuthToken } from '../api/client';

const GuestContext = createContext({
  isGuest: false,
  enterGuestMode: () => {},
  exitGuestMode: () => {},
});

export function GuestProvider({ children }) {
  const [isGuest, setIsGuest] = useState(false);

  const enterGuestMode = useCallback(() => {
    try {
      clearAuthTokens();
    } catch {
      // ignore storage errors
    }
    setAuthToken(null);
    setIsGuest(true);
  }, []);

  const exitGuestMode = useCallback(() => setIsGuest(false), []);
  const value = useMemo(
    () => ({ isGuest, enterGuestMode, exitGuestMode }),
    [isGuest, enterGuestMode, exitGuestMode]
  );
  return <GuestContext.Provider value={value}>{children}</GuestContext.Provider>;
}

export function useGuest() {
  return useContext(GuestContext);
}
