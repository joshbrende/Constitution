import { useEffect, useState } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { apiClient, setAuthToken } from '../api/client';
import { getAppConfig } from '../api/appConfigApi';
import { saveAuthTokens } from '../api/authStorage';
import { catchMessage } from '../lib/apiErrors';
import { openLegal } from '../lib/legalLinks';
import { sanitizeRegisterPayload } from '../lib/sanitize';
import { inputBorderClass, registerSchema, validateForm } from '../lib/validation';
import { useGuest } from '../context/GuestContext';
import { registerDeviceWebPush } from '../lib/webPush';
import AuthLayout from '../components/AuthLayout';

export default function RegisterPage() {
  const navigate = useNavigate();
  const { exitGuestMode } = useGuest();
  const [appConfig, setAppConfig] = useState(null);
  const [form, setForm] = useState({
    name: '',
    surname: '',
    email: '',
    password: '',
    password_confirmation: '',
    accept_terms: false,
  });
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState(null);
  const [fieldErrors, setFieldErrors] = useState({});

  useEffect(() => {
    getAppConfig()
      .then(setAppConfig)
      .catch(() => {});
  }, []);

  function update(field, value) {
    setForm((prev) => ({ ...prev, [field]: value }));
  }

  async function handleSubmit(e) {
    e.preventDefault();
    setError(null);
    setFieldErrors({});
    const validation = validateForm(registerSchema, sanitizeRegisterPayload(form));
    if (!validation.success) {
      setFieldErrors(validation.fieldErrors);
      setError(validation.firstError);
      return;
    }
    try {
      setLoading(true);
      const response = await apiClient.post('/auth/register', validation.data);
      setAuthToken(response.data.access_token);
      await saveAuthTokens(response.data.access_token, response.data.refresh_token);
      exitGuestMode();
      registerDeviceWebPush().catch(() => {});
      navigate('/home', { replace: true });
    } catch (err) {
      setError(catchMessage(err, 'Registration failed. Please review your details.'));
    } finally {
      setLoading(false);
    }
  }

  return (
    <AuthLayout>
      <h1 className="text-center text-[22px] font-bold text-app-text">Create account</h1>
      <p className="mb-4 mt-1.5 text-center text-sm text-app-subtle">
        Register to access the Constitution, Academy learning, and tools.
      </p>
      <form onSubmit={handleSubmit} className="space-y-3">
        <div className="grid grid-cols-2 gap-2">
          <div>
            <label className="mb-1 block text-xs text-gray-200">Name</label>
            <input
              type="text"
              value={form.name}
              onChange={(e) => update('name', e.target.value)}
              className={`w-full rounded-[10px] border bg-app-bg px-2.5 py-2 text-sm ${inputBorderClass(fieldErrors.name)}`}
            />
            {fieldErrors.name ? <p className="text-[11px] text-red-300">{fieldErrors.name}</p> : null}
          </div>
          <div>
            <label className="mb-1 block text-xs text-gray-200">Surname</label>
            <input
              type="text"
              value={form.surname}
              onChange={(e) => update('surname', e.target.value)}
              className={`w-full rounded-[10px] border bg-app-bg px-2.5 py-2 text-sm ${inputBorderClass(fieldErrors.surname)}`}
            />
            {fieldErrors.surname ? <p className="text-[11px] text-red-300">{fieldErrors.surname}</p> : null}
          </div>
        </div>
        <div>
          <label className="mb-1 block text-xs text-gray-200">Email</label>
          <input
            type="email"
            value={form.email}
            onChange={(e) => update('email', e.target.value)}
            placeholder="you@example.org.zw"
            className={`w-full rounded-[10px] border bg-app-bg px-2.5 py-2 text-sm ${inputBorderClass(fieldErrors.email)}`}
          />
          {fieldErrors.email ? <p className="text-[11px] text-red-300">{fieldErrors.email}</p> : null}
        </div>
        <div>
          <label className="mb-1 block text-xs text-gray-200">Password</label>
          <input
            type="password"
            value={form.password}
            onChange={(e) => update('password', e.target.value)}
            className={`w-full rounded-[10px] border bg-app-bg px-2.5 py-2 text-sm ${inputBorderClass(fieldErrors.password)}`}
          />
          {fieldErrors.password ? <p className="text-[11px] text-red-300">{fieldErrors.password}</p> : null}
        </div>
        <div>
          <label className="mb-1 block text-xs text-gray-200">Retype password</label>
          <input
            type="password"
            value={form.password_confirmation}
            onChange={(e) => update('password_confirmation', e.target.value)}
            className={`w-full rounded-[10px] border bg-app-bg px-2.5 py-2 text-sm ${inputBorderClass(fieldErrors.password_confirmation)}`}
          />
          {fieldErrors.password_confirmation ? (
            <p className="text-[11px] text-red-300">{fieldErrors.password_confirmation}</p>
          ) : null}
        </div>
        <label className="flex items-start gap-2 text-xs text-app-subtle">
          <input
            type="checkbox"
            checked={form.accept_terms}
            onChange={(e) => update('accept_terms', e.target.checked)}
            className="mt-0.5"
          />
          <span>
            I agree to the{' '}
            <button type="button" onClick={() => openLegal('terms', appConfig)} className="text-app-gold underline">
              terms
            </button>
            ,{' '}
            <button type="button" onClick={() => openLegal('privacy', appConfig)} className="text-app-gold underline">
              privacy policy
            </button>
            , and{' '}
            <button type="button" onClick={() => openLegal('cookies', appConfig)} className="text-app-gold underline">
              cookies policy
            </button>
          </span>
        </label>
        {fieldErrors.accept_terms ? (
          <p className="text-[11px] text-red-300">{fieldErrors.accept_terms}</p>
        ) : null}
        {error ? <p className="text-xs text-app-error">{error}</p> : null}
        <button
          type="submit"
          disabled={loading}
          className="w-full rounded-full bg-app-green py-2.5 text-sm font-semibold text-white disabled:opacity-70"
        >
          {loading ? 'Creating…' : 'Register'}
        </button>
      </form>
      <Link to="/login" className="mt-4 block text-center text-sm text-app-gold">
        Already have an account? Sign in
      </Link>
    </AuthLayout>
  );
}
