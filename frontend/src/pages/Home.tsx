import { useEffect, useState } from 'react';
import { Link } from 'react-router-dom';
import { useLocale } from '../i18n/LocaleContext';
import { getProperties, getPropertyCabins } from '../services/catalog';
import type { Cabin, Property } from '../types/api';
import { CabinCard } from '../components/CabinCard';
import { HomeSkeleton } from '../components/HomeSkeleton';

type PageStatus = 'loading' | 'ready' | 'empty' | 'error';

/**
 * Public Home page. Single-property MVP: the site renders the available
 * active property. Cabin loading degrades independently — a cabins failure
 * keeps property content visible with a section-level retry.
 */
function Home() {
  const { t } = useLocale();
  const [status, setStatus] = useState<PageStatus>('loading');
  const [property, setProperty] = useState<Property | null>(null);
  const [cabins, setCabins] = useState<Cabin[]>([]);
  const [cabinsError, setCabinsError] = useState(false);
  const [reloadKey, setReloadKey] = useState(0);

  useEffect(() => {
    let cancelled = false;
    async function load() {
      setStatus('loading');
      setCabinsError(false);
      try {
        const properties = await getProperties();
        if (cancelled) return;
        const first = properties.data[0] ?? null;
        if (!first) {
          setProperty(null);
          setCabins([]);
          setStatus('empty');
          return;
        }
        try {
          const result = await getPropertyCabins(first.slug);
          if (cancelled) return;
          setProperty(first);
          setCabins(result.data);
          setCabinsError(false);
          setStatus('ready');
        } catch {
          if (cancelled) return;
          setProperty(first);
          setCabins([]);
          setCabinsError(true);
          setStatus('ready');
        }
      } catch {
        if (cancelled) return;
        setProperty(null);
        setCabins([]);
        setStatus('error');
      }
    }
    void load();
    return () => {
      cancelled = true;
    };
  }, [reloadKey]);

  function retry() {
    setReloadKey((key) => key + 1);
  }

  if (status === 'loading') {
    return (
      <main className="min-h-svh bg-neutral-50 font-sans text-neutral-950 antialiased">
        <HomeSkeleton />
      </main>
    );
  }

  if (status === 'error' || status === 'empty') {
    const isEmpty = status === 'empty';
    return (
      <main className="min-h-svh bg-neutral-50 font-sans text-neutral-950 antialiased">
        <div className="mx-auto max-w-2xl px-6 py-24 text-center">
          <h1 className="font-display text-4xl text-forest-800">
            {t(isEmpty ? 'home.emptyTitle' : 'home.errorTitle')}
          </h1>
          <p className="mt-4 text-neutral-700">
            {t(isEmpty ? 'home.emptyBody' : 'home.errorBody')}
          </p>
          {!isEmpty ? (
            <p className="mt-8">
              <button
                type="button"
                onClick={retry}
                className="rounded-lg bg-forest-700 px-5 py-3 text-sm text-white"
              >
                {t('common.retry')}
              </button>
            </p>
          ) : null}
        </div>
      </main>
    );
  }

  if (!property) return null;

  const highlights = Array.from(
    new Map(
      cabins
        .flatMap((cabin) => cabin.amenities ?? [])
        .map((amenity) => [amenity.slug, amenity]),
    ).values(),
  ).slice(0, 6);

  return (
    <main className="min-h-svh bg-neutral-50 font-sans text-neutral-950 antialiased">
      <section aria-labelledby="home-hero" className="bg-forest-900 text-white">
        <div className="mx-auto max-w-5xl px-6 py-24">
          <p className="text-sm uppercase tracking-widest text-forest-100">
            {t('home.eyebrow')}
          </p>
          <h1 id="home-hero" className="mt-4 font-display text-5xl">
            {property.name}
          </h1>
          {property.description ? (
            <p className="mt-4 max-w-2xl text-lg text-neutral-100">
              {property.description}
            </p>
          ) : null}
          <p className="mt-8">
            <Link
              to="/booking"
              className="rounded-lg bg-accent-600 px-6 py-3 text-sm text-white"
            >
              {t('home.checkAvailability')}
            </Link>
          </p>
        </div>
      </section>

      {property.description ? (
        <section aria-labelledby="home-intro" className="mx-auto max-w-5xl px-6 py-16">
          <h2 id="home-intro" className="font-display text-3xl text-forest-800">
            {t('home.introTitle')}
          </h2>
          <p className="mt-4 max-w-3xl text-lg leading-relaxed text-neutral-700">
            {property.description}
          </p>
        </section>
      ) : null}

      <section aria-labelledby="home-cabins" className="mx-auto max-w-5xl px-6 pb-16">
        <h2 id="home-cabins" className="font-display text-3xl text-forest-800">
          {t('home.cabinsTitle')}
        </h2>
        {cabinsError ? (
          <div role="alert" className="mt-6 rounded-xl bg-error-bg p-6">
            <p className="font-medium text-error">{t('home.cabinsErrorTitle')}</p>
            <p className="mt-2 text-sm text-neutral-700">{t('home.cabinsErrorBody')}</p>
            <p className="mt-4">
              <button
                type="button"
                onClick={retry}
                className="rounded-lg bg-forest-700 px-5 py-3 text-sm text-white"
              >
                {t('common.retry')}
              </button>
            </p>
          </div>
        ) : cabins.length === 0 ? (
          <p className="mt-6 text-neutral-700">{t('home.cabinsEmpty')}</p>
        ) : (
          <div className="mt-8 grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
            {cabins.map((cabin) => (
              <CabinCard key={cabin.id} cabin={cabin} propertySlug={property.slug} />
            ))}
          </div>
        )}
      </section>

      {highlights.length > 0 ? (
        <section aria-labelledby="home-highlights" className="mx-auto max-w-5xl px-6 pb-16">
          <h2 id="home-highlights" className="font-display text-3xl text-forest-800">
            {t('home.highlightsTitle')}
          </h2>
          <ul className="mt-6 flex flex-wrap gap-3">
            {highlights.map((amenity) => (
              <li
                key={amenity.slug}
                className="rounded-full border border-neutral-300 bg-white px-4 py-2 text-sm text-neutral-900"
              >
                {amenity.name}
              </li>
            ))}
          </ul>
        </section>
      ) : null}

      <section aria-labelledby="home-final-cta" className="bg-forest-100">
        <div className="mx-auto max-w-5xl px-6 py-16 text-center">
          <h2 id="home-final-cta" className="font-display text-3xl text-forest-800">
            {t('home.finalCtaTitle')}
          </h2>
          <p className="mt-6">
            <Link
              to="/booking"
              className="rounded-lg bg-forest-700 px-6 py-3 text-sm text-white"
            >
              {t('home.checkAvailability')}
            </Link>
          </p>
        </div>
      </section>
    </main>
  );
}

export default Home;
