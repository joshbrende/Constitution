import axios from 'axios';
import { clearAuthTokens, getAuthTokens, saveAuthTokens } from './authStorage';
import { describeApiError } from '../lib/apiErrors';

const AUTH_FLOW_PATHS = new Set(['/splash', '/login', '/register', '/forgot-password']);

const BASE_URL = import.meta.env.VITE_API_BASE_URL || '/api/v1';

export const apiClient = axios.create({
  baseURL: BASE_URL,
  headers: { Accept: 'application/json' },
  // Cold PHP-FPM on Windows bind mounts can exceed 10s on first request.
  timeout: 45000,
});

let sessionExpiredHandler = null;

export function setSessionExpiredHandler(handler) {
  sessionExpiredHandler = handler;
}

export function setAuthToken(token) {
  if (token) {
    apiClient.defaults.headers.common.Authorization = `Bearer ${token}`;
  } else {
    delete apiClient.defaults.headers.common.Authorization;
  }
}

/** Restore token from localStorage before any React effect runs (avoids 401 race on /home). */
export function bootstrapAuthTokenFromStorage() {
  try {
    const token =
      localStorage.getItem('auth_access_token') ||
      localStorage.getItem('@auth_access_token');
    if (token) setAuthToken(token);
    return token;
  } catch {
    return null;
  }
}

bootstrapAuthTokenFromStorage();

let refreshInFlight = null;

async function refreshSessionTokens() {
  if (refreshInFlight) {
    return refreshInFlight;
  }

  refreshInFlight = (async () => {
    const { refreshToken } = await getAuthTokens();
    if (!refreshToken) {
      return null;
    }

    const refreshRes = await apiClient.post('/auth/refresh', {
      refresh_token: refreshToken,
    });
    const newAccessToken = refreshRes.data?.access_token;
    const newRefreshToken = refreshRes.data?.refresh_token;
    if (!newAccessToken || !newRefreshToken) {
      return null;
    }

    await saveAuthTokens(newAccessToken, newRefreshToken);
    setAuthToken(newAccessToken);
    return { accessToken: newAccessToken, refreshToken: newRefreshToken };
  })().finally(() => {
    refreshInFlight = null;
  });

  return refreshInFlight;
}

function isAuthFlowPath() {
  const path = window.location.pathname.replace(/^\/app/, '') || '/';
  return AUTH_FLOW_PATHS.has(path);
}

export function getApiRootUrl() {
  const base = apiClient.defaults.baseURL || '';
  if (!base) return '';
  if (base.startsWith('/')) {
    return window.location.origin;
  }
  return base.replace(/\/api\/v1\/?$/i, '').replace(/\/$/, '');
}

export function getApiBaseUrl() {
  return apiClient.defaults.baseURL || '';
}

export function absoluteMediaUrl(url) {
  if (!url || typeof url !== 'string') return null;
  if (url.startsWith('http://') || url.startsWith('https://')) return url;
  const root = getApiRootUrl();
  if (!root) return url;
  return `${root}${url.startsWith('/') ? '' : '/'}${url}`;
}

apiClient.interceptors.response.use(
  (response) => response,
  async (error) => {
    const status = error?.response?.status;
    const retryAfter = error?.response?.headers?.['retry-after'];
    const msg = error?.response?.data?.message;
    const code = error?.code;

    if (code === 'ECONNABORTED' || error?.message?.includes('timeout')) {
      error.message = 'The server took too long to respond. Please try again.';
    } else if (status === 429) {
      error.message = retryAfter
        ? `Too many requests. Please try again in ${retryAfter} seconds.`
        : 'Too many requests. Please try again in a moment.';
    } else if (status === 503) {
      error.message = retryAfter
        ? `High demand. We'll retry in ${retryAfter} seconds.`
        : msg || 'Service temporarily busy. Please try again.';
    } else if (status === 401) {
      const originalConfig = error?.config;
      const requestUrl = typeof originalConfig?.url === 'string' ? originalConfig.url : '';

      if (requestUrl.includes('/auth/login')) {
        error.message = msg || 'Invalid email or password.';
        const friendly = describeApiError(error);
        error.userMessage = friendly.message;
        return Promise.reject(error);
      }

      const isRefreshRequest = requestUrl.includes('/auth/refresh');
      const hadAuthHeader = Boolean(
        originalConfig?.headers?.Authorization || originalConfig?.headers?.authorization
      );

      // If a request raced ahead of bootstrap, attach stored token and retry once.
      if (!isRefreshRequest && originalConfig && !hadAuthHeader && !originalConfig._retryAuthHeader) {
        originalConfig._retryAuthHeader = true;
        const stored = bootstrapAuthTokenFromStorage();
        if (stored) {
          originalConfig.headers = {
            ...(originalConfig.headers || {}),
            Authorization: `Bearer ${stored}`,
          };
          return apiClient.request(originalConfig);
        }
      }

      if (!isRefreshRequest && originalConfig && !originalConfig._retryAuthRefresh) {
        originalConfig._retryAuthRefresh = true;
        try {
          const refreshed = await refreshSessionTokens();
          if (refreshed?.accessToken) {
            originalConfig.headers = {
              ...(originalConfig.headers || {}),
              Authorization: `Bearer ${refreshed.accessToken}`,
            };
            return apiClient.request(originalConfig);
          }
        } catch {
          // fall through
        }
      }

      try {
        await clearAuthTokens();
      } catch {
        // ignore
      }
      setAuthToken(null);

      if (!isAuthFlowPath() && sessionExpiredHandler) {
        sessionExpiredHandler();
      }
      error.message = msg || 'Your session has expired. Please sign in again.';
    }

    const friendly = describeApiError(error);
    error.userMessage = friendly.message;
    error.apiKind = friendly.kind;
    return Promise.reject(error);
  }
);
