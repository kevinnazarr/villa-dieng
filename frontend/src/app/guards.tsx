import { Navigate, Outlet, useLocation } from 'react-router-dom';
import { useAuth } from '../auth/AuthContext';

/**
 * Auth-aware guards. Loading and transient-error states never redirect —
 * only a resolved guest does. Admin role comes from GET /me; server-side
 * EnsureAdmin remains authoritative.
 */

function AuthLoading() {
  return (
    <main aria-busy="true" aria-live="polite">
      <p>Loading…</p>
    </main>
  );
}

function AuthErrorState() {
  const { error, refresh } = useAuth();
  return (
    <main>
      <p role="alert">
        {error?.message ??
          'We could not reach the server. Your session was kept.'}
      </p>
      <button type="button" onClick={() => void refresh()}>
        Try again
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
