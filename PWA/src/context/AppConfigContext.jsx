import { createContext, useContext, useEffect, useMemo, useState } from 'react';
import { getAppConfig } from '../api/appConfigApi';

const AppConfigContext = createContext({
  config: null,
  loading: true,
  dialogueEnabled: true,
});

export function AppConfigProvider({ children }) {
  const [config, setConfig] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    getAppConfig()
      .then(setConfig)
      .catch(() => setConfig(null))
      .finally(() => setLoading(false));
  }, []);

  const dialogueEnabled = config?.features?.enable_dialogue !== false;

  const value = useMemo(
    () => ({ config, loading, dialogueEnabled }),
    [config, loading, dialogueEnabled]
  );

  return <AppConfigContext.Provider value={value}>{children}</AppConfigContext.Provider>;
}

export function useAppConfig() {
  return useContext(AppConfigContext);
}
