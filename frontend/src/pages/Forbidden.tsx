import { Link } from 'react-router-dom';

export function Forbidden() {
  return (
    <main className="min-h-svh bg-neutral-50 font-sans text-neutral-950 antialiased">
      <div className="mx-auto max-w-2xl px-6 py-24 text-center">
        <h1 className="font-display text-4xl text-forest-800">
          Access forbidden
        </h1>
        <p className="mt-4 text-neutral-700">
          You do not have permission to view this page.
        </p>
        <p className="mt-8 flex justify-center gap-4">
          <Link
            to="/"
            className="rounded-lg bg-forest-700 px-5 py-3 text-sm text-white"
          >
            Back to home
          </Link>
          <Link
            to="/account"
            className="rounded-lg border border-neutral-300 px-5 py-3 text-sm text-neutral-900"
          >
            Go to account
          </Link>
        </p>
      </div>
    </main>
  );
}
