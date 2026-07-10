import { getApiRootUrl } from '../api/client';

export function openLegal(kind, appConfig) {
  const legal = appConfig?.legal;
  const mapped =
    kind === 'terms'
      ? legal?.terms_url
      : kind === 'privacy'
        ? legal?.privacy_url
        : kind === 'cookies'
          ? legal?.cookies_url
          : null;

  if (typeof mapped === 'string' && mapped.length > 0) {
    window.open(mapped, '_blank', 'noopener,noreferrer');
    return;
  }

  const base = getApiRootUrl();
  const path =
    kind === 'terms' ? '/terms-of-use' : kind === 'privacy' ? '/privacy-policy' : '/cookies';
  window.open(`${base}${path}`, '_blank', 'noopener,noreferrer');
}
