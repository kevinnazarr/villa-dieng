import { Link, Outlet } from 'react-router-dom';

/**
 * Structural shells only — no final nav/header/footer design yet.
 * Each layout renders its section landmark and the child route outlet.
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

export function CustomerLayout() {
  return (
    <>
      <header>
        <nav aria-label="Account navigation">
          <Link to="/">Cabin Villa Dieng</Link>
        </nav>
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
      </header>
      <Outlet />
    </>
  );
}
