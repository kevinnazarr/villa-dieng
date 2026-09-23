/**
 * Static loading skeleton for the Cabin Detail page. Blocks only —
 * no shimmer, no animation.
 */
export function CabinDetailSkeleton() {
  return (
    <div aria-busy="true" aria-live="polite">
      <div className="mx-auto max-w-5xl px-6 py-8">
        <div className="h-4 w-56 rounded bg-neutral-200" />
      </div>
      <div className="mx-auto max-w-5xl px-6">
        <div className="aspect-video w-full rounded-xl bg-neutral-200" />
        <div className="mt-4 grid grid-cols-3 gap-4">
          {[0, 1, 2].map((index) => (
            <div key={index} className="aspect-[4/3] w-full rounded-xl bg-neutral-200" />
          ))}
        </div>
      </div>
      <div className="mx-auto max-w-5xl px-6 py-12">
        <div className="h-12 w-2/3 rounded bg-neutral-200" />
        <div className="mt-4 h-4 w-1/3 rounded bg-neutral-200" />
        <div className="mt-2 h-6 w-1/4 rounded bg-neutral-200" />
        <div className="mt-6 h-4 w-full rounded bg-neutral-200" />
        <div className="mt-2 h-4 w-5/6 rounded bg-neutral-200" />
        <div className="mt-8 h-12 w-48 rounded-lg bg-neutral-200" />
      </div>
    </div>
  );
}
