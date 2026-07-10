import { useEffect, useState } from 'react';
import { Link } from 'react-router-dom';
import { getAppConfig } from '../api/appConfigApi';

export default function AboutPage() {
  const [config, setConfig] = useState(null);

  useEffect(() => {
    getAppConfig().then(setConfig).catch(() => setConfig(null));
  }, []);

  return (
    <div className="space-y-4 p-4 text-sm">
      <h2 className="text-xl font-bold">About ZANUPF App</h2>
      <p className="text-app-subtle">
        Progressive Web App — member portal for the Constitution, Academy, and party resources.
      </p>
      <p>
        Version: <span className="text-app-gold">{import.meta.env.VITE_APP_VERSION ?? '1.0.0'}</span>
      </p>
      {config?.support_email ? (
        <p>
          Support:{' '}
          <a href={`mailto:${config.support_email}`} className="text-app-gold">
            {config.support_email}
          </a>
        </p>
      ) : null}
      <div className="space-y-2 pt-2">
        <Link to="/pages/help" className="block text-app-gold">
          Help
        </Link>
        <Link to="/pages/privacy" className="block text-app-gold">
          Privacy policy
        </Link>
        <Link to="/pages/terms" className="block text-app-gold">
          Terms of use
        </Link>
      </div>
    </div>
  );
}
