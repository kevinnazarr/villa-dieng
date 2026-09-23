import { Navigate, Outlet, useLocation } from 'react-router-dom';
import { useAuth } from '../auth/AuthContext';
import { useLocale } from '../i18n/LocaleContext';

/**
 * Auth-aware guards. Loading and transient-error states never redirect —
 * only a resolved guest does. Admin role comes from GET /me; server-side
 * EnsureAdmin remains authoritative.
 */

function AuthLoading() {
  const { t } = useLocale();
  return (
    <main aria-busy="true" aria-live="polite">
      <p>{t('common.loading')}</p>
    </main>
  );
}

function AuthErrorState() {
  const { error, refresh } = useAuth();
  const { t } = useLocale();
  return (
    <main>
      <p role="alert">{error?.message ?? t('common.authErrorFallback')}</p>
      <button type="button" onClick={() => void refresh()}>
        {t('common.retry')}
      </button>
    </main>
  );
}

export function RequireAuth() {
  const { status } = useAuth();
  const location = useLocation();
  if (status === 'loading') return <AuthLoading />;
  if (status === 'error') return <AuthErrorState />;
  if (status !== 'authenticated') {
    return <Navigate to="/login" replace state={{ from: location.pathname }} />;
  }
  return <Outlet />;
}

export function RequireAdmin() {
  const { status, user } = useAuth();
  const location = useLocation();
  if (status === 'loading') return <AuthLoading />;
  if (status === 'error') return <AuthErrorState />;
  if (status !== 'authenticated') {
    return <Navigate to="/login" replace state={{ from: location.pathname }} />;
  }
  if (user?.role !== 'admin') {
    return <Navigate to="/forbidden" replace />;
  }
  return <Outlet />;
}
