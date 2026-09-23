import { Link, Outlet, useNavigate } from 'react-router-dom';
import { useAuth } from '../auth/AuthContext';

/**
 * Structural shells only — no final nav/header/footer design yet.
 * Customer/admin layouts show a minimal identity affordance (name +
 * sign-out) wired to AuthContext. Each layout renders its section
 * landmark and the child route outlet.
 */

export function PublicLayout() {
  return (
    <>
      <header>
        <nav aria-label="Main navigation">
          <Link to="/">Cabin Villa Dieng</Link>
        </nav>
      </header>
      <Outlet />
      <footer>
        <p>Cabin Villa Dieng</p>
      </footer>
    </>
  );
}

function SignOutButton() {
  const { user, logout } = useAuth();
  const navigate = useNavigate();
  async function handleSignOut() {
    await logout();
    navigate('/', { replace: true });
  }
  return (
    <p>
      {user ? <span>{user.name} </span> : null}
      <button type="button" onClick={() => void handleSignOut()}>
        Sign out
      </button>
    </p>
  );
}

export function CustomerLayout() {
  return (
    <>
      <header>
        <nav aria-label="Account navigation">
          <Link to="/">Cabin Villa Dieng</Link>
        </nav>
        <SignOutButton />
      </header>
      <Outlet />
    </>
  );
}

export function AdminLayout() {
  return (
    <>
      <header>
        <nav aria-label="Admin navigation">
          <Link to="/">Cabin Villa Dieng</Link>
        </nav>
        <SignOutButton />
      </header>
      <Outlet />
    </>
  );
}
