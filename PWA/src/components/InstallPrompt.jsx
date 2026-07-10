import { useEffect, useState } from 'react';
import WorkflowIcon from '../ui/icons/WorkflowIcon';
import { WORKFLOW_ICON_KEYS } from '../ui/icons/workflowIcons';

export default function InstallPrompt() {
  const [deferred, setDeferred] = useState(null);
  const [dismissed, setDismissed] = useState(false);

  useEffect(() => {
    if (localStorage.getItem('pwa_install_dismissed') === '1') {
      setDismissed(true);
      return undefined;
    }

    function onBeforeInstall(e) {
      e.preventDefault();
      setDeferred(e);
    }

    window.addEventListener('beforeinstallprompt', onBeforeInstall);
    return () => window.removeEventListener('beforeinstallprompt', onBeforeInstall);
  }, []);

  if (dismissed || !deferred) return null;

  async function install() {
    deferred.prompt();
    await deferred.userChoice;
    setDeferred(null);
  }

  function dismiss() {
    localStorage.setItem('pwa_install_dismissed', '1');
    setDismissed(true);
    setDeferred(null);
  }

  return (
    <div className="mx-3 mb-2 flex items-center gap-3 rounded-xl border border-app-gold/40 bg-app-surface p-3">
      <WorkflowIcon iconKey={WORKFLOW_ICON_KEYS.certificateDownload} size={22} />
      <div className="min-w-0 flex-1">
        <p className="text-sm font-semibold">Install ZANUPF app</p>
        <p className="text-xs text-app-subtle">Add to your home screen for quick access.</p>
      </div>
      <button type="button" onClick={install} className="shrink-0 rounded-full bg-app-green px-3 py-1 text-xs font-semibold text-white">
        Install
      </button>
      <button type="button" onClick={dismiss} aria-label="Dismiss" className="shrink-0 text-app-muted">
        <WorkflowIcon iconKey={WORKFLOW_ICON_KEYS.readerClose} variant="muted" size={18} />
      </button>
    </div>
  );
}
