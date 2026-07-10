import Pusher from 'pusher-js';
import { getAppConfig } from '../api/appConfigApi';
import { getAuthTokens } from '../api/authStorage';

let sharedPusher = null;
let sharedConfigKey = null;

function resolveReverbHost(configHost) {
  const envHost = import.meta.env.VITE_REVERB_HOST;
  if (envHost) return envHost;

  const host = configHost || 'localhost';
  if (host === 'localhost' || host === '127.0.0.1') {
    const pageHost = typeof window !== 'undefined' ? window.location.hostname : '';
    if (pageHost && pageHost !== 'localhost' && pageHost !== '127.0.0.1') {
      return pageHost;
    }
  }

  return host;
}

function resolveRealtimeConfig(appConfig) {
  const cfg = appConfig?.realtime;
  if (!cfg?.enabled || !cfg?.key) return null;

  const devPort = import.meta.env.VITE_REVERB_PORT;
  const devScheme = import.meta.env.VITE_REVERB_SCHEME;

  return {
    key: cfg.key,
    host: resolveReverbHost(cfg.host),
    port: Number(devPort || cfg.port || 8090),
    scheme: devScheme || cfg.scheme || 'http',
    authEndpoint: cfg.auth_endpoint,
  };
}

/** Ported from mobile/src/lib/dialoguePusher.js */
export async function getSharedDialoguePusher() {
  const data = await getAppConfig();
  const cfg = resolveRealtimeConfig(data);
  if (!cfg) return null;

  const configKey = `${cfg.key}|${cfg.host}|${cfg.port}|${cfg.scheme}`;
  if (sharedPusher && sharedConfigKey === configKey) return sharedPusher;

  if (sharedPusher) {
    sharedPusher.disconnect();
    sharedPusher = null;
  }

  const { accessToken } = await getAuthTokens();
  if (!accessToken) return null;

  const authEndpoint = cfg.authEndpoint?.startsWith('http')
    ? cfg.authEndpoint
    : `${window.location.origin}${cfg.authEndpoint}`;

  sharedPusher = new Pusher(cfg.key, {
    wsHost: cfg.host,
    wsPort: cfg.port,
    wssPort: cfg.port,
    forceTLS: cfg.scheme === 'https',
    enabledTransports: ['ws', 'wss'],
    disableStats: true,
    cluster: 'mt1',
    authEndpoint,
    auth: {
      headers: {
        Authorization: `Bearer ${accessToken}`,
        Accept: 'application/json',
      },
    },
  });

  sharedConfigKey = configKey;
  return sharedPusher;
}

export async function subscribeDialogueThread(threadId, onMessage) {
  if (!threadId) return null;

  const pusher = await getSharedDialoguePusher();
  if (!pusher) return null;

  const channelName = `private-dialogue.thread.${threadId}`;
  const channel = pusher.subscribe(channelName);

  const handler = (payload) => {
    if (payload?.message) onMessage(payload.message);
  };

  channel.bind('message.changed', handler);

  return {
    unsubscribe() {
      channel.unbind('message.changed', handler);
      pusher.unsubscribe(channelName);
    },
  };
}
