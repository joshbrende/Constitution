/**
 * Ported from mobile/src/utils/apiErrors.js
 */
const GENERIC = {
  kind: 'generic',
  title: 'Something went wrong',
  message: 'Please try again. If the problem continues, check your connection and try later.',
};

export function describeApiError(error) {
  if (!error || typeof error !== 'object') {
    return { ...GENERIC, error: 'unknown' };
  }

  if (error.code === 'ECONNABORTED' || error.message?.includes('timeout')) {
    return {
      kind: 'timeout',
      title: 'Request timed out',
      message: 'The server took too long to respond. Check your connection and try again.',
      error: 'timeout',
    };
  }

  if (!error.response) {
    return {
      kind: 'network',
      title: 'No connection',
      message: 'We could not reach the server. Check your internet connection and try again.',
      error: 'network_offline',
    };
  }

  const status = error.response.status;
  const data = error.response.data;
  const serverCode = data?.error;
  const serverMsg =
    typeof data?.message === 'string' && data.message.trim() !== '' ? data.message : null;

  if (status === 401) {
    return {
      kind: 'session',
      title: 'Session expired',
      message: serverMsg || 'Please sign in again to continue.',
      error: 'unauthenticated',
    };
  }
  if (status === 422) {
    return {
      kind: 'validation',
      title: 'Check your input',
      message: serverMsg || 'Some fields need to be corrected.',
      error: 'validation_failed',
      errors: data?.errors,
    };
  }

  if (serverMsg && !/Request failed with status code|Network Error|timeout/i.test(serverMsg)) {
    return { kind: 'generic', title: 'Request failed', message: serverMsg, error: serverCode || 'http_error' };
  }

  return { ...GENERIC, error: serverCode || 'unknown' };
}

export function catchMessage(error, fallback) {
  if (error?.userMessage) return error.userMessage;
  const data = error?.response?.data;
  if (data?.errors && typeof data.errors === 'object') {
    const first = Object.values(data.errors).flat()[0];
    if (typeof first === 'string' && first.trim() !== '') return first;
  }
  if (typeof data?.message === 'string' && data.message.trim() !== '') return data.message;
  return fallback;
}
