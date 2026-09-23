import { useState, type FormEvent } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { useAuth } from '../auth/AuthContext';
import { useLocale } from '../i18n/LocaleContext';
import { isApiError } from '../lib/errors';

export function Register() {
  const { register } = useAuth();
  const { t } = useLocale();
  const navigate = useNavigate();
  const [name, setName] = useState('');
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [passwordConfirmation, setPasswordConfirmation] = useState('');
  const [fieldErrors, setFieldErrors] = useState<Record<string, string[]>>({});
  const [formError, setFormError] = useState<string | null>(null);
  const [submitting, setSubmitting] = useState(false);

  function fieldError(field: string): string | undefined {
    return fieldErrors[field]?.[0];
  }

  async function handleSubmit(event: FormEvent) {
    event.preventDefault();
    setFieldErrors({});
    setFormError(null);
    setSubmitting(true);
    try {
      await register({
        name: name.trim(),
        email: email.trim(),
        password,
        password_confirmation: passwordConfirmation,
      });
      navigate('/account', { replace: true });
    } catch (err) {
      if (isApiError(err)) {
        if (err.status === 422 && err.errors) {
          setFieldErrors(err.errors);
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
          setFormError(err.message || 'Registration failed. Please try again.');
        }
      } else {
        setFormError('Registration failed. Please try again.');
      }
    } finally {
      setSubmitting(false);
    }
  }

  return (
    <main className="min-h-svh bg-neutral-50 font-sans text-neutral-950 antialiased">
      <div className="mx-auto max-w-md px-6 py-24">
        <h1 className="font-display text-4xl text-forest-800">
          {t('auth.createAccount')}
        </h1>
        <form onSubmit={handleSubmit} noValidate className="mt-8">
          <div>
            <label htmlFor="register-name">{t('auth.fullName')}</label>
            <input
              id="register-name"
              type="text"
              autoComplete="name"
              required
              value={name}
              onChange={(e) => setName(e.target.value)}
              aria-describedby={
                fieldError('name') ? 'register-name-error' : undefined
              }
              className="mt-1 w-full rounded-lg border border-neutral-300 bg-white px-4 py-3"
            />
            {fieldError('name') ? (
              <p id="register-name-error" className="mt-1 text-sm text-error">
                {fieldError('name')}
              </p>
            ) : null}
          </div>
          <div className="mt-4">
            <label htmlFor="register-email">{t('auth.email')}</label>
            <input
              id="register-email"
              type="email"
              autoComplete="email"
              required
              value={email}
              onChange={(e) => setEmail(e.target.value)}
              aria-describedby={
                fieldError('email') ? 'register-email-error' : undefined
              }
              className="mt-1 w-full rounded-lg border border-neutral-300 bg-white px-4 py-3"
            />
            {fieldError('email') ? (
              <p id="register-email-error" className="mt-1 text-sm text-error">
                {fieldError('email')}
              </p>
            ) : null}
          </div>
          <div className="mt-4">
            <label htmlFor="register-password">{t('auth.password')}</label>
            <input
              id="register-password"
              type="password"
              autoComplete="new-password"
              required
              minLength={8}
              value={password}
              onChange={(e) => setPassword(e.target.value)}
              aria-describedby={
                fieldError('password') ? 'register-password-error' : undefined
              }
              className="mt-1 w-full rounded-lg border border-neutral-300 bg-white px-4 py-3"
            />
            {fieldError('password') ? (
              <p
                id="register-password-error"
                className="mt-1 text-sm text-error"
              >
                {fieldError('password')}
              </p>
            ) : null}
          </div>
          <div className="mt-4">
            <label htmlFor="register-password-confirmation">
              {t('auth.confirmPassword')}
            </label>
            <input
              id="register-password-confirmation"
              type="password"
              autoComplete="new-password"
              required
              value={passwordConfirmation}
              onChange={(e) => setPasswordConfirmation(e.target.value)}
              className="mt-1 w-full rounded-lg border border-neutral-300 bg-white px-4 py-3"
            />
          </div>
          {formError ? (
            <p role="alert" className="mt-4 text-error">
              {formError}
            </p>
          ) : null}
          <button
            type="submit"
            disabled={submitting}
            aria-busy={submitting}
            className="mt-6 w-full rounded-lg bg-forest-700 px-5 py-3 text-sm text-white disabled:opacity-60"
          >
            {submitting ? t('auth.creatingAccount') : t('auth.createAccount')}
          </button>
        </form>
        <p className="mt-6 text-center text-sm text-neutral-700">
          {t('auth.haveAccount')} <Link to="/login">{t('auth.signIn')}</Link>
        </p>
      </div>
    </main>
  );
}
