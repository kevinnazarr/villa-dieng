import { useLocale } from '../i18n/LocaleContext';
import type { TranslationKey } from '../i18n/dictionaries';

interface PlaceholderPageProps {
  titleKey: TranslationKey;
}

/**
 * Honest stand-in for routes whose features belong to later commits.
 * States the page exists and its feature is not implemented yet.
 */
export function PlaceholderPage({ titleKey }: PlaceholderPageProps) {
  const { t } = useLocale();
  return (
    <main className="min-h-svh bg-neutral-50 font-sans text-neutral-950 antialiased">
      <p className="mx-auto max-w-2xl px-6 py-24 text-center">
        <span className="font-display text-4xl text-forest-800">
          {t(titleKey)}
        </span>
      </p>
      <p className="mx-auto max-w-2xl px-6 pb-24 text-center text-neutral-700">
        {t('placeholder.body')}
      </p>
    </main>
  );
}
