/** Ported from mobile/src/lib/sanitize.js */
export function trimText(input) {
  if (input == null || typeof input !== 'string') return '';
  return input.trim();
}

export function normalizeEmail(email) {
  return trimText(email).toLowerCase();
}

export function normalizeNationalIdInput(raw) {
  if (raw == null) return '';
  const cleaned = String(raw).toUpperCase().replace(/[^0-9A-Z-]/g, '');
  const noDashes = cleaned.replace(/-/g, '');
  if (noDashes.length <= 2) return noDashes;
  return `${noDashes.slice(0, 2)}-${noDashes.slice(2)}`;
}

export function normalizePlainText(input) {
  return trimText(input).replace(/\s+/g, ' ');
}

export function sanitizeRegisterPayload(data) {
  return {
    name: normalizePlainText(data.name),
    surname: normalizePlainText(data.surname),
    email: normalizeEmail(data.email),
    password: data.password ?? '',
    password_confirmation: data.password_confirmation ?? '',
    accept_terms: Boolean(data.accept_terms),
  };
}

export function sanitizeLoginPayload(data) {
  return {
    email: normalizeEmail(data.email),
    password: data.password ?? '',
  };
}

export function sanitizeProfilePayload(data) {
  const nationalId = trimText(data.national_id);
  return {
    national_id: nationalId === '' ? null : nationalId,
    province_id: data.province_id ?? null,
  };
}

export function sanitizeForgotPasswordPayload(data) {
  return { email: normalizeEmail(data.email) };
}

export function sanitizeDialogueMessage(body) {
  return trimText(body);
}
