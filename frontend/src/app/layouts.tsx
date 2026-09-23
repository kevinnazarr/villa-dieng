import { Link, Outlet, useNavigate } from 'react-router-dom';
import { useAuth } from '../auth/AuthContext';
import { useLocale } from '../i18n/LocaleContext';
import { LocaleSwitcher } from '../components/LocaleSwitcher';

/**
 * Structural shells only — no final nav/header/footer design yet.
 * Customer/admin layouts show a minimal identity affordance (name +
 * sign-out) wired to AuthContext. Each layout renders its section
 * landmark and the child route outlet.
 */

export function PublicLayout() {
  const { t } = useLocale();
  return (
    <>
      <header>
        <nav aria-label={t('nav.main')}>
          <Link to="/">{t('nav.brand')}</Link>
        </nav>
        <LocaleSwitcher />
      </header>
      <Outlet />
      <footer>
        <p>{t('footer.brand')}</p>
      </footer>
    </>
  );
}

function SignOutButton() {
  const { user, logout } = useAuth();
  const { t } = useLocale();
  const navigate = useNavigate();
  async function handleSignOut() {
    await logout();
    navigate('/', { replace: true });
  }
  return (
    <p>
      {user ? <span>{user.name} </span> : null}
      <button type="button" onClick={() => void handleSignOut()}>
        {t('auth.signOut')}
      </button>
    </p>
  );
}

export function CustomerLayout() {
  const { t } = useLocale();
  return (
    <>
      <header>
        <nav aria-label={t('nav.account')}>
          <Link to="/">{t('nav.brand')}</Link>
        </nav>
        <LocaleSwitcher />
        <SignOutButton />
      </header>
      <Outlet />
    </>
  );
}

export function AdminLayout() {
  const { t } = useLocale();
  return (
    <>
      <header>
        <nav aria-label={t('nav.admin')}>
          <Link to="/">{t('nav.brand')}</Link>
        </nav>
        <LocaleSwitcher />
        <SignOutButton />
      </header>
      <Outlet />
    </>
  );
}
