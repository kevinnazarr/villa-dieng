import { useState, type FormEvent } from 'react';
import { Link, useLocation, useNavigate } from 'react-router-dom';
import { useAuth } from '../auth/AuthContext';
import { useLocale } from '../i18n/LocaleContext';
import { isApiError } from '../lib/errors';

function safeRedirect(from: unknown): string {
  if (typeof from === 'string' && from.startsWith('/') && !from.startsWith('//')) {
    return from;
  }
  return '/account';
}

export function Login() {
  const { login } = useAuth();
  const { t } = useLocale();
  const navigate = useNavigate();
  const location = useLocation();
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [formError, setFormError] = useState<string | null>(null);
  const [submitting, setSubmitting] = useState(false);

  async function handleSubmit(event: FormEvent) {
    event.preventDefault();
    setFormError(null);
    setSubmitting(true);
    try {
      await login(email.trim(), password);
      const from = (location.state as { from?: unknown } | null)?.from;
      navigate(safeRedirect(from), { replace: true });
    } catch (err) {
      if (isApiError(err)) {
        if (err.status === 422) {
          const emailErrors = err.errors?.['email'];
          setFormError(
            emailErrors?.[0] ?? err.message ?? 'Invalid credentials.',
          );
        } else if (err.status === 429) {
          setFormError(
            err.retryAfter !== undefined
              ? `Too many attempts. Please try again in ${err.retryAfter} seconds.`
              : 'Too many attempts. Please try again later.',
          );
        } else if (err.status === 0) {
          setFormError(
            'Network error. Please check your connection and try again.',
          );
        } else {
          setFormError(err.message || 'Sign in failed. Please try again.');
        }
      } else {
        setFormError('Sign in failed. Please try again.');
      }
    } finally {
      setSubmitting(false);
    }
  }

  return (
    <main className="min-h-svh bg-neutral-50 font-sans text-neutral-950 antialiased">
      <div className="mx-auto max-w-md px-6 py-24">
        <h1 className="font-display text-4xl text-forest-800">
          {t('auth.signIn')}
        </h1>
        <form onSubmit={handleSubmit} noValidate className="mt-8">
          <div>
            <label htmlFor="login-email">{t('auth.email')}</label>
            <input
              id="login-email"
              type="email"
              autoComplete="email"
              required
              value={email}
              onChange={(e) => setEmail(e.target.value)}
              aria-describedby="login-error"
              className="mt-1 w-full rounded-lg border border-neutral-300 bg-white px-4 py-3"
            />
          </div>
          <div className="mt-4">
            <label htmlFor="login-password">{t('auth.password')}</label>
            <input
              id="login-password"
              type="password"
              autoComplete="current-password"
              required
              value={password}
              onChange={(e) => setPassword(e.target.value)}
              aria-describedby="login-error"
              className="mt-1 w-full rounded-lg border border-neutral-300 bg-white px-4 py-3"
            />
          </div>
          {formError ? (
            <p id="login-error" role="alert" className="mt-4 text-error">
              {formError}
            </p>
          ) : null}
          <button
            type="submit"
            disabled={submitting}
            aria-busy={submitting}
            className="mt-6 w-full rounded-lg bg-forest-700 px-5 py-3 text-sm text-white disabled:opacity-60"
          >
            {submitting ? t('auth.signingIn') : t('auth.signIn')}
          </button>
        </form>
        <p className="mt-6 text-center text-sm text-neutral-700">
          {t('auth.noAccount')} <Link to="/register">{t('auth.createOne')}</Link>
        </p>
      </div>
    </main>
  );
}
