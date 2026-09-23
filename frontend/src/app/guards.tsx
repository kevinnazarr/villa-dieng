import { Navigate, Outlet, useLocation } from 'react-router-dom';
import { getToken } from '../services/token';

/**
 * Minimal guard infrastructure for this phase.
 *
 * Customer routes require a stored Sanctum token; otherwise redirect to
 * /login preserving the intended destination.
 *
 * Admin routes require a token too, but role authorization stays
 * server-side (`EnsureAdmin` → 403). The opaque Sanctum token carries no
 * client-readable role, and AuthContext (Commit 5) owns identity — so this
 * guard deliberately does NOT check roles and does not duplicate backend
 * authorization rules.
 */

export function RequireAuth() {
  const location = useLocation();
  if (!getToken()) {
    return <Navigate to="/login" replace state={{ from: location.pathname }} />;
  }
  return <Outlet />;
}

export function RequireAdmin() {
  const location = useLocation();
  if (!getToken()) {
    return <Navigate to="/login" replace state={{ from: location.pathname }} />;
  }
  // Role check deferred to AuthContext + server-side EnsureAdmin.
  return <Outlet />;
}
