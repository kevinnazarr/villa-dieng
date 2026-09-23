/**
 * Static loading skeleton for the Home page. Blocks only — no shimmer,
 * no animation — so `prefers-reduced-motion` needs no special handling.
 */
export function HomeSkeleton() {
  return (
    <div aria-busy="true" aria-live="polite">
      <div className="bg-forest-900">
        <div className="mx-auto max-w-5xl px-6 py-24">
          <div className="h-4 w-48 rounded bg-neutral-200" />
          <div className="mt-6 h-12 w-3/4 rounded bg-neutral-200" />
          <div className="mt-4 h-4 w-1/2 rounded bg-neutral-200" />
          <div className="mt-8 h-12 w-48 rounded-lg bg-neutral-200" />
        </div>
      </div>
      <div className="mx-auto max-w-5xl px-6 py-16">
        <div className="h-8 w-56 rounded bg-neutral-200" />
        <div className="mt-4 h-4 w-full rounded bg-neutral-200" />
        <div className="mt-2 h-4 w-2/3 rounded bg-neutral-200" />
      </div>
      <div className="mx-auto max-w-5xl px-6 pb-16">
        <div className="h-8 w-40 rounded bg-neutral-200" />
        <div className="mt-8 grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
          {[0, 1, 2].map((index) => (
            <div key={index}>
              <div className="aspect-[4/3] w-full rounded-xl bg-neutral-200" />
              <div className="mt-4 h-6 w-2/3 rounded bg-neutral-200" />
              <div className="mt-2 h-4 w-1/2 rounded bg-neutral-200" />
            </div>
          ))}
        </div>
      </div>
    </div>
  );
}
