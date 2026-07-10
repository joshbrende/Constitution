import { useState } from 'react';
import { Link } from 'react-router-dom';
import { apiClient } from '../api/client';
import { catchMessage } from '../lib/apiErrors';
import { sanitizeForgotPasswordPayload } from '../lib/sanitize';
import { forgotPasswordSchema, inputBorderClass, validateForm } from '../lib/validation';
import AuthLayout from '../components/AuthLayout';

export default function ForgotPasswordPage() {
  const [email, setEmail] = useState('');
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState(null);
  const [success, setSuccess] = useState(null);
  const [fieldErrors, setFieldErrors] = useState({});

  async function handleSubmit(e) {
    e.preventDefault();
    setError(null);
    setSuccess(null);
    const validation = validateForm(forgotPasswordSchema, sanitizeForgotPasswordPayload({ email }));
    if (!validation.success) {
      setFieldErrors(validation.fieldErrors);
      setError(validation.firstError);
      return;
    }
    try {
      setLoading(true);
      await apiClient.post('/auth/forgot-password', validation.data);
      setSuccess('If that email is registered, you will receive reset instructions shortly.');
    } catch (err) {
      setError(catchMessage(err, 'Could not send reset email.'));
    } finally {
      setLoading(false);
    }
  }

  return (
    <AuthLayout>
      <h1 className="text-center text-[22px] font-bold text-app-text">Forgot password</h1>
      <p className="mb-4 mt-2 text-center text-sm text-app-subtle">
        Enter your email and we will send reset instructions.
      </p>
      <form onSubmit={handleSubmit} className="space-y-3">
        <div>
          <label className="mb-1 block text-xs text-gray-200">Email</label>
          <input
            type="email"
            value={email}
            onChange={(e) => setEmail(e.target.value)}
            placeholder="you@example.org.zw"
            className={`w-full rounded-[10px] border bg-app-bg px-2.5 py-2 text-sm ${inputBorderClass(fieldErrors.email)}`}
          />
          {fieldErrors.email ? <p className="text-[11px] text-red-300">{fieldErrors.email}</p> : null}
        </div>
        {error ? <p className="text-xs text-app-error">{error}</p> : null}
        {success ? <p className="text-xs text-green-300">{success}</p> : null}
        <button
          type="submit"
          disabled={loading}
          className="w-full rounded-full bg-app-green py-2.5 text-sm font-semibold text-white disabled:opacity-70"
        >
          {loading ? 'Sending…' : 'Send reset link'}
        </button>
      </form>
      <Link to="/login" className="mt-4 block text-center text-sm text-app-gold">
        Back to sign in
      </Link>
    </AuthLayout>
  );
}
