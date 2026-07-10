import { useState } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { apiClient, setAuthToken } from '../api/client';
import { saveAuthTokens } from '../api/authStorage';
import { catchMessage } from '../lib/apiErrors';
import { sanitizeLoginPayload } from '../lib/sanitize';
import { inputBorderClass, loginSchema, validateForm } from '../lib/validation';
import { useGuest } from '../context/GuestContext';
import AuthLayout from '../components/AuthLayout';

export default function LoginPage() {
  const navigate = useNavigate();
  const { exitGuestMode, enterGuestMode } = useGuest();
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState(null);
  const [fieldErrors, setFieldErrors] = useState({});

  async function handleLogin(e) {
    e.preventDefault();
    setError(null);
    setFieldErrors({});

    const validation = validateForm(loginSchema, sanitizeLoginPayload({ email, password }));
    if (!validation.success) {
      setFieldErrors(validation.fieldErrors);
      setError(validation.firstError);
      return;
    }

    try {
      setLoading(true);
      const response = await apiClient.post('/auth/login', validation.data);
      setAuthToken(response.data.access_token);
      await saveAuthTokens(response.data.access_token, response.data.refresh_token);
      exitGuestMode();
      // Web Push permission is requested from Profile — avoid iOS Safari dialogs
      // interrupting navigation right after login.
      navigate('/home', { replace: true });
    } catch (err) {
      setError(catchMessage(err, 'Login failed. Please check your credentials.'));
    } finally {
      setLoading(false);
    }
  }

  function browseAsGuest() {
    enterGuestMode();
    setAuthToken(null);
    navigate('/home', { replace: true });
  }

  return (
    <AuthLayout>
      <h1 className="text-center text-[22px] font-bold text-app-text">Sign in</h1>
      <p className="mb-[18px] mt-1.5 text-center text-sm text-app-subtle">
        Use your credentials to access the Constitution, Academy, and Library.
      </p>

      <form onSubmit={handleLogin} className="space-y-3">
        <div>
          <label className="mb-1 block text-xs text-gray-200">Email</label>
          <input
            type="email"
            value={email}
            onChange={(e) => setEmail(e.target.value)}
            placeholder="you@example.org.zw"
            autoComplete="email"
            className={`w-full rounded-[10px] border bg-app-bg px-2.5 py-2 text-sm text-app-text outline-none focus:border-app-green ${inputBorderClass(fieldErrors.email)}`}
          />
          {fieldErrors.email ? (
            <p className="mt-0.5 text-[11px] text-red-300">{fieldErrors.email}</p>
          ) : null}
        </div>

        <div>
          <label className="mb-1 block text-xs text-gray-200">Password</label>
          <input
            type="password"
            value={password}
            onChange={(e) => setPassword(e.target.value)}
            placeholder="••••••••"
            autoComplete="current-password"
            className={`w-full rounded-[10px] border bg-app-bg px-2.5 py-2 text-sm text-app-text outline-none focus:border-app-green ${inputBorderClass(fieldErrors.password)}`}
          />
          {fieldErrors.password ? (
            <p className="mt-0.5 text-[11px] text-red-300">{fieldErrors.password}</p>
          ) : null}
        </div>

        {error ? <p className="text-xs text-app-error">{error}</p> : null}

        <button
          type="submit"
          disabled={loading}
          className="mt-2 w-full rounded-full bg-app-green py-2.5 text-sm font-semibold text-white disabled:opacity-70"
        >
          {loading ? 'Signing in…' : 'Sign in'}
        </button>
      </form>

      <div className="mt-3 space-y-3 text-center">
        <Link to="/forgot-password" className="block text-[13px] text-app-gold">
          Forgot password?
        </Link>
        <Link to="/register" className="block text-[13px] text-app-gold">
          Need an account? Register
        </Link>
      </div>

      <button
        type="button"
        onClick={browseAsGuest}
        className="mt-3.5 w-full border-t border-app-border pt-3 text-[13px] text-slate-300 underline"
      >
        Browse constitution without signing in
      </button>
    </AuthLayout>
  );
}
