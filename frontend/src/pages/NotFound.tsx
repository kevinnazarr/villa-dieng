import { Link } from 'react-router-dom';

export function NotFound() {
  return (
    <main className="min-h-svh bg-neutral-50 font-sans text-neutral-950 antialiased">
      <div className="mx-auto max-w-2xl px-6 py-24 text-center">
        <h1 className="font-display text-4xl text-forest-800">Page not found</h1>
        <p className="mt-4 text-neutral-700">
          The page you are looking for does not exist or has moved.
        </p>
        <p className="mt-8">
          <Link
            to="/"
            className="rounded-lg bg-forest-700 px-5 py-3 text-sm text-white"
          >
            Back to home
          </Link>
        </p>
      </div>
    </main>
  );
}
