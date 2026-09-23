import { Link } from 'react-router-dom';
import { useLocale } from '../i18n/LocaleContext';
import { formatCurrency } from '../i18n/format';
import { isUsableImageUrl, pickPrimaryImage } from '../lib/images';
import type { Cabin } from '../types/api';

interface CabinCardProps {
  cabin: Cabin;
  propertySlug: string;
}

/**
 * Presentational cabin card. Receives resolved display data via props —
 * no fetching, no auth awareness. Image renders only for usable absolute
 * or root-relative URLs; bare storage paths fall back to a placeholder.
 */
export function CabinCard({ cabin, propertySlug }: CabinCardProps) {
  const { locale, t } = useLocale();
  const image = pickPrimaryImage(cabin.images);
  const imageUrl = image && isUsableImageUrl(image.path) ? image.path : null;
  const alt = image?.alt_text ?? t('home.imageFallback', { name: cabin.name });

  return (
    <article aria-labelledby={`cabin-${cabin.id}`}>
      {imageUrl ? (
        <img
          src={imageUrl}
          alt={alt}
          loading="lazy"
          decoding="async"
          className="aspect-[4/3] w-full rounded-xl object-cover"
        />
      ) : (
        <div
          aria-hidden="true"
          className="flex aspect-[4/3] w-full items-center justify-center rounded-xl bg-forest-100"
        >
          <span className="font-display text-5xl text-forest-700">
            {cabin.name.charAt(0)}
          </span>
        </div>
      )}
      <h3 id={`cabin-${cabin.id}`} className="mt-4 text-xl text-neutral-900">
        {cabin.name}
      </h3>
      <p className="mt-1 text-sm text-neutral-700">
        {t('home.capacity', { adults: cabin.max_adults, children: cabin.max_children })}
      </p>
      <p className="mt-2 text-base font-medium text-neutral-950">
        {formatCurrency(locale, cabin.base_price, cabin.currency)}
        <span className="font-normal text-neutral-700">{t('home.perNight')}</span>
      </p>
      <p className="mt-4">
        <Link
          to={`/cabin/${propertySlug}/${cabin.slug}`}
          className="rounded-lg bg-forest-700 px-5 py-3 text-sm text-white"
        >
          {t('home.checkAvailability')}
        </Link>
      </p>
    </article>
  );
}
