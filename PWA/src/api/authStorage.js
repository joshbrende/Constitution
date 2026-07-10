/**
 * Auth token storage — mirrors mobile/src/api/authStorage.js (web path uses localStorage).
 */
const ACCESS_TOKEN_KEY = 'auth_access_token';
const REFRESH_TOKEN_KEY = 'auth_refresh_token';
const LEGACY_ACCESS_KEY = '@auth_access_token';
const LEGACY_REFRESH_KEY = '@auth_refresh_token';

function readToken(key, legacyKey) {
  try {
    return localStorage.getItem(key) || localStorage.getItem(legacyKey) || null;
  } catch {
    return null;
  }
}

function writeToken(key, value) {
  if (!value) return;
  try {
    localStorage.setItem(key, value);
  } catch {
    // ignore
  }
}

function deleteToken(key, legacyKey) {
  try {
    localStorage.removeItem(key);
    localStorage.removeItem(legacyKey);
  } catch {
    // ignore
  }
}

export async function saveAuthTokens(accessToken, refreshToken) {
  writeToken(ACCESS_TOKEN_KEY, accessToken);
  writeToken(REFRESH_TOKEN_KEY, refreshToken);
  localStorage.removeItem(LEGACY_ACCESS_KEY);
  localStorage.removeItem(LEGACY_REFRESH_KEY);
}

export async function getAuthTokens() {
  return {
    accessToken: readToken(ACCESS_TOKEN_KEY, LEGACY_ACCESS_KEY),
    refreshToken: readToken(REFRESH_TOKEN_KEY, LEGACY_REFRESH_KEY),
  };
}

export async function clearAuthTokens() {
  deleteToken(ACCESS_TOKEN_KEY, LEGACY_ACCESS_KEY);
  deleteToken(REFRESH_TOKEN_KEY, LEGACY_REFRESH_KEY);
}

export async function getAuthToken() {
  const { accessToken } = await getAuthTokens();
  return accessToken;
}
