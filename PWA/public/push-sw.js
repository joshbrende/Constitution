/* Web Push handlers for ZANUPF PWA (imported by Workbox service worker). */

self.addEventListener('push', (event) => {
  let payload = { title: 'ZANUPF', body: '', data: {}, url: '/app/home/notifications' };
  try {
    payload = { ...payload, ...event.data?.json() };
  } catch {
    payload.body = event.data?.text() || '';
  }

  const origin = self.location.origin;
  const url = payload.url?.startsWith('http')
    ? payload.url
    : `${origin}${payload.url || '/app/home/notifications'}`;

  event.waitUntil(
    self.registration.showNotification(payload.title || 'ZANUPF', {
      body: payload.body || '',
      icon: `${origin}/app/icon-192.png`,
      badge: `${origin}/app/icon-192.png`,
      tag: payload.data?.type ? `push-${payload.data.type}` : 'zanupf-push',
      data: { ...payload.data, url },
    })
  );
});

self.addEventListener('notificationclick', (event) => {
  event.notification.close();
  const targetUrl = event.notification.data?.url || `${self.location.origin}/app/home/notifications`;

  event.waitUntil(
    self.clients.matchAll({ type: 'window', includeUncontrolled: true }).then((clients) => {
      for (const client of clients) {
        if (client.url.startsWith(self.location.origin) && 'focus' in client) {
          if ('navigate' in client) {
            return client.navigate(targetUrl).then(() => client.focus());
          }
          client.focus();
          return undefined;
        }
      }
      return self.clients.openWindow(targetUrl);
    })
  );
});
