import { useEffect, useState } from 'react';
import { Link, useParams } from 'react-router-dom';
import { useLocale } from '../i18n/LocaleContext';
import { formatCurrency } from '../i18n/format';
import { getCabinDetail } from '../services/catalog';
import { usableImages } from '../lib/images';
import { isApiError } from '../lib/errors';
import type { Cabin } from '../types/api';
import { CabinDetailSkeleton } from '../components/CabinDetailSkeleton';

type DetailStatus = 'loading' | 'ready' | 'notFound' | 'error';

export function CabinDetail() {
  const { propertySlug, cabinSlug } = useParams();
  const { locale, t } = useLocale();
  const [status, setStatus] = useState<DetailStatus>('loading');
  const [cabin, setCabin] = useState<Cabin | null>(null);
  const [reloadKey, setReloadKey] = useState(0);

  useEffect(() => {
    let cancelled = false;
    async function load() {
      if (!propertySlug || !cabinSlug) {
        if (cancelled) return;
        setCabin(null);
        setStatus('notFound');
        return;
      }
      setStatus('loading');
      try {
        const result = await getCabinDetail(propertySlug, cabinSlug);
        if (cancelled) return;
        setCabin(result.data);
        setStatus('ready');
      } catch (err) {
        if (cancelled) return;
        setCabin(null);
        setStatus(isApiError(err) && err.status === 404 ? 'notFound' : 'error');
      }
    }
    void load();
    return () => {
      cancelled = true;
    };
  }, [propertySlug, cabinSlug, reloadKey]);

  function retry() {
    setReloadKey((key) => key + 1);
  }

  if (status === 'loading') {
    return (
      <main className="min-h-svh bg-neutral-50 font-sans text-neutral-950 antialiased">
        <CabinDetailSkeleton />
      </main>
    );
  }

  if (status === 'notFound' || status === 'error') {
    const notFound = status === 'notFound';
    return (
      <main className="min-h-svh bg-neutral-50 font-sans text-neutral-950 antialiased">
        <div className="mx-auto max-w-2xl px-6 py-24 text-center">
          <h1 className="font-display text-4xl text-forest-800">
            {t(notFound ? 'cabin.notFoundTitle' : 'cabin.errorTitle')}
          </h1>
          <p className="mt-4 text-neutral-700">
            {t(notFound ? 'cabin.notFoundBody' : 'cabin.errorBody')}
          </p>
          <p className="mt-8 flex justify-center gap-4">
            {!notFound ? (
              <button
                type="button"
                onClick={retry}
                className="rounded-lg bg-forest-700 px-5 py-3 text-sm text-white"
              >
                {t('common.retry')}
              </button>
            ) : null}
            <Link
              to="/"
              className="rounded-lg border border-neutral-300 px-5 py-3 text-sm text-neutral-900"
            >
              {t('common.backHome')}
            </Link>
          </p>
        </div>
      </main>
    );
  }

  if (!cabin) return null;

  const images = usableImages(cabin.images);
  const [mainImage, ...secondaryImages] = images;
  const visibleSecondary = secondaryImages.slice(0, 3);
  const property = cabin.property;
  const amenities = cabin.amenities ?? [];

  return (
    <main className="min-h-svh bg-neutral-50 font-sans text-neutral-950 antialiased">
      <div className="mx-auto max-w-5xl px-6 py-8">
        <nav aria-label={t('cabin.breadcrumb')}>
          <ol className="flex flex-wrap gap-2 text-sm text-neutral-700">
            <li>
              <Link to="/">{property?.name ?? t('nav.brand')}</Link>
            </li>
            <li aria-hidden="true">/</li>
            <li aria-current="page" className="text-neutral-950">
              {cabin.name}
            </li>
          </ol>
        </nav>
      </div>

      <section aria-labelledby="cabin-gallery" className="mx-auto max-w-5xl px-6">
        <h2 id="cabin-gallery" className="sr-only">
          {t('cabin.gallery')}
        </h2>
        {mainImage ? (
          <div>
            <img
              src={mainImage.path}
              alt={mainImage.alt_text ?? t('home.imageFallback', { name: cabin.name })}
              decoding="async"
              className="aspect-video w-full rounded-xl object-cover"
            />
            {visibleSecondary.length > 0 ? (
              <div className="mt-4 grid grid-cols-3 gap-4">
                {visibleSecondary.map((image) => (
                  <img
                    key={image.id}
                    src={image.path}
                    alt={image.alt_text ?? t('home.imageFallback', { name: cabin.name })}
                    loading="lazy"
                    decoding="async"
                    className="aspect-4/3 w-full rounded-xl object-cover"
                  />
                ))}
              </div>
            ) : null}
          </div>
        ) : (
          <div
            aria-hidden="true"
            className="flex aspect-video w-full items-center justify-center rounded-xl bg-forest-100"
          >
            <span className="font-display text-7xl text-forest-700">
              {cabin.name.charAt(0)}
            </span>
          </div>
        )}
      </section>

      <section aria-labelledby="cabin-header" className="mx-auto max-w-5xl px-6 py-12">
        <h1 id="cabin-header" className="font-display text-5xl text-forest-800">
          {cabin.name}
        </h1>
        <p className="mt-3 text-neutral-700">
          {t('home.capacity', { adults: cabin.max_adults, children: cabin.max_children })}
        </p>
        <p className="mt-2 text-xl font-medium text-neutral-950">
          {formatCurrency(locale, cabin.base_price, cabin.currency)}
          <span className="font-normal text-neutral-700">{t('home.perNight')}</span>
        </p>
        {cabin.description ? (
          <p className="mt-6 max-w-3xl text-lg leading-relaxed text-neutral-700">
            {cabin.description}
          </p>
        ) : null}
        <p className="mt-8">
          <Link
            to={`/booking?property=${encodeURIComponent(propertySlug ?? '')}&cabin=${encodeURIComponent(cabin.slug)}`}
            className="rounded-lg bg-accent-600 px-6 py-3 text-sm text-white"
          >
            {t('home.checkAvailability')}
          </Link>
        </p>
      </section>

      {amenities.length > 0 ? (
        <section aria-labelledby="cabin-amenities" className="mx-auto max-w-5xl px-6 pb-12">
          <h2 id="cabin-amenities" className="font-display text-3xl text-forest-800">
            {t('cabin.amenities')}
          </h2>
          <ul className="mt-6 flex flex-wrap gap-3">
            {amenities.map((amenity) => (
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

      {property ? (
        <section aria-labelledby="cabin-property" className="bg-forest-100">
          <div className="mx-auto max-w-5xl px-6 py-12">
            <h2 id="cabin-property" className="font-display text-2xl text-forest-800">
              {t('cabin.inProperty', { property: property.name })}
            </h2>
            {property.description ? (
              <p className="mt-3 max-w-3xl leading-relaxed text-neutral-700">
                {property.description}
              </p>
            ) : null}
          </div>
        </section>
      ) : null}
    </main>
  );
}
