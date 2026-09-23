interface PlaceholderPageProps {
  title: string;
}

/**
 * Honest stand-in for routes whose features belong to later commits.
 * States the page exists and its feature is not implemented yet.
 */
export function PlaceholderPage({ title }: PlaceholderPageProps) {
  return (
    <main className="min-h-svh bg-neutral-50 font-sans text-neutral-950 antialiased">
      <p className="mx-auto max-w-2xl px-6 py-24 text-center">
        <span className="font-display text-4xl text-forest-800">{title}</span>
      </p>
      <p className="mx-auto max-w-2xl px-6 pb-24 text-center text-neutral-700">
        This page is part of the application. Its feature is not implemented
        yet.
      </p>
    </main>
  );
}
