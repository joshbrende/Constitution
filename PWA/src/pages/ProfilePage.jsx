import { useEffect, useState } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { apiClient, setAuthToken } from '../api/client';
import { clearAuthTokens } from '../api/authStorage';
import { deleteAccount, getProfile, updateProfile } from '../api/profileApi';
import { getProvinces } from '../api/provincesApi';
import { catchMessage } from '../lib/apiErrors';
import { normalizeNationalIdInput, sanitizeProfilePayload } from '../lib/sanitize';
import { inputBorderClass, profileMemberSaveSchema, validateForm } from '../lib/validation';
import {
  getNotificationPermission,
  notificationPermissionLabel,
  requestNotificationPermission,
} from '../lib/browserNotifications';
import { useGuest } from '../context/GuestContext';
import { registerDeviceWebPush, unregisterDeviceWebPush, isWebPushSupported } from '../lib/webPush';

export default function ProfilePage() {
  const navigate = useNavigate();
  const { isGuest } = useGuest();
  const [user, setUser] = useState(null);
  const [nationalId, setNationalId] = useState('');
  const [provinceId, setProvinceId] = useState('');
  const [provinces, setProvinces] = useState([]);
  const [loading, setLoading] = useState(true);
  const [saving, setSaving] = useState(false);
  const [error, setError] = useState(null);
  const [success, setSuccess] = useState(null);
  const [fieldErrors, setFieldErrors] = useState({});
  const [notifPermission, setNotifPermission] = useState(() => getNotificationPermission());
  const [notifLoading, setNotifLoading] = useState(false);

  useEffect(() => {
    if (isGuest) {
      navigate('/login', { replace: true });
    }
  }, [isGuest, navigate]);

  useEffect(() => {
    Promise.all([getProfile(), getProvinces().catch(() => [])])
      .then(([u, provs]) => {
        setUser(u);
        setNationalId(u?.national_id ?? '');
        setProvinceId(u?.province_id ? String(u.province_id) : '');
        setProvinces(Array.isArray(provs) ? provs : []);
      })
      .catch((e) => setError(catchMessage(e, 'Failed to load profile.')))
      .finally(() => setLoading(false));
  }, []);

  async function handleSave(e) {
    e.preventDefault();
    setSaving(true);
    setError(null);
    setSuccess(null);
    setFieldErrors({});
    const payload = sanitizeProfilePayload({
      national_id: nationalId,
      province_id: provinceId ? Number(provinceId) : null,
    });
    const validation = validateForm(profileMemberSaveSchema, payload);
    if (!validation.success) {
      setFieldErrors(validation.fieldErrors);
      setError(validation.firstError);
      setSaving(false);
      return;
    }
    try {
      const updated = await updateProfile(validation.data);
      setUser(updated);
      setSuccess('Profile updated.');
    } catch (err) {
      setError(catchMessage(err, 'Failed to save profile.'));
    } finally {
      setSaving(false);
    }
  }

  async function handleLogout() {
    if (!window.confirm('Are you sure you want to sign out?')) return;
    try {
      await apiClient.post('/auth/logout');
    } catch {
      // ignore
    }
    await unregisterDeviceWebPush().catch(() => {});
    await clearAuthTokens();
    setAuthToken(null);
    navigate('/login', { replace: true });
  }

  async function handleDelete() {
    if (!window.confirm('Delete your account permanently? This cannot be undone.')) return;
    try {
      await deleteAccount();
    } catch {
      // ignore
    }
    await unregisterDeviceWebPush().catch(() => {});
    await clearAuthTokens();
    setAuthToken(null);
    navigate('/login', { replace: true });
  }

  async function handleEnableNotifications() {
    setNotifLoading(true);
    const result = await requestNotificationPermission();
    setNotifPermission(result);
    if (result === 'granted' && isWebPushSupported()) {
      try {
        await registerDeviceWebPush();
      } catch {
        // non-fatal
      }
    }
    setNotifLoading(false);
  }

  if (loading) return <p className="p-4 text-sm text-app-subtle">Loading…</p>;

  return (
    <div className="space-y-4 p-4">
      <div className="rounded-xl border border-app-border bg-app-card p-4">
        <p className="text-lg font-semibold">
          {user?.name} {user?.surname}
        </p>
        <p className="text-sm text-app-subtle">{user?.email}</p>
        {user?.membership_number ? (
          <p className="mt-2 text-sm text-app-text">
            Membership no. <span className="font-mono">{user.membership_number}</span>
          </p>
        ) : null}
        {Array.isArray(user?.active_wings) && user.active_wings.length > 0 ? (
          <p className="mt-1 text-sm text-app-subtle">
            Leagues:{' '}
            {user.active_wings
              .map((w) => (w === 'main' ? 'Main' : `${w.charAt(0).toUpperCase()}${w.slice(1)}`))
              .join(', ')}
          </p>
        ) : null}
        {user?.membership_standing === 'member' ? (
          <Link to="/members" className="mt-3 inline-block text-sm text-app-gold underline">
            Browse member directory
          </Link>
        ) : null}
      </div>

      <form onSubmit={handleSave} className="space-y-3 rounded-xl border border-app-border bg-app-card p-4">
        <h2 className="font-semibold">Membership details</h2>
        <div>
          <label className="mb-1 block text-xs text-gray-200">National ID</label>
          <input
            value={nationalId}
            onChange={(e) => setNationalId(normalizeNationalIdInput(e.target.value))}
            className={`w-full rounded-[10px] border bg-app-bg px-2.5 py-2 text-sm ${inputBorderClass(fieldErrors.national_id)}`}
          />
          {fieldErrors.national_id ? (
            <p className="text-[11px] text-red-300">{fieldErrors.national_id}</p>
          ) : null}
        </div>
        <div>
          <label className="mb-1 block text-xs text-gray-200">Province</label>
          <select
            value={provinceId}
            onChange={(e) => setProvinceId(e.target.value)}
            className="w-full rounded-[10px] border border-app-border bg-app-bg px-2.5 py-2 text-sm"
          >
            <option value="">Select province</option>
            {provinces.map((p) => (
              <option key={p.id} value={p.id}>
                {p.name}
              </option>
            ))}
          </select>
        </div>
        {error ? <p className="text-xs text-app-error">{error}</p> : null}
        {success ? <p className="text-xs text-green-300">{success}</p> : null}
        <button
          type="submit"
          disabled={saving}
          className="w-full rounded-full bg-app-green py-2 text-sm font-semibold text-white"
        >
          {saving ? 'Saving…' : 'Save profile'}
        </button>
      </form>

      <div className="rounded-xl border border-app-border bg-app-card p-4">
        <h2 className="font-semibold">Notifications</h2>
        <p className="mt-1 text-xs text-app-subtle">
          Browser and background push alerts for academy updates and admin messages.
        </p>
        <p className="mt-2 text-sm text-app-text">
          Status: {notificationPermissionLabel(notifPermission)}
        </p>
        {notifPermission !== 'granted' && notifPermission !== 'unsupported' ? (
          <button
            type="button"
            onClick={handleEnableNotifications}
            disabled={notifLoading || notifPermission === 'denied'}
            className="mt-3 w-full rounded-full bg-app-gold py-2 text-sm font-semibold text-black disabled:opacity-50"
          >
            {notifLoading ? 'Requesting…' : 'Enable browser notifications'}
          </button>
        ) : null}
      </div>

      <button
        type="button"
        onClick={handleLogout}
        className="w-full rounded-full border border-app-border py-2 text-sm text-app-text"
      >
        Sign out
      </button>
      <button
        type="button"
        onClick={handleDelete}
        className="w-full rounded-full border border-red-900 py-2 text-sm text-red-300"
      >
        Delete account
      </button>
    </div>
  );
}
