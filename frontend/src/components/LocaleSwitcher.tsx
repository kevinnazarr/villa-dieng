import { useId } from 'react';
import { useLocale } from '../i18n/LocaleContext';
import { isLocale } from '../i18n/locale';

/**
 * Minimal ID/EN switcher. Native select: keyboard accessible with visible
 * focus from the global `:focus-visible` token. Associated label keeps the
 * control understandable to assistive technology.
 */
export function LocaleSwitcher() {
  const { locale, setLocale, t } = useLocale();
  const labelId = useId();
  return (
    <p>
      <label htmlFor={labelId}>{t('nav.language')}</label>{' '}
      <select
        id={labelId}
        value={locale}
        onChange={(event) => {
          const next = event.target.value;
          if (isLocale(next)) setLocale(next);
        }}
      >
        <option value="id">{t('nav.language.id')}</option>
        <option value="en">{t('nav.language.en')}</option>
      </select>
    </p>
  );
}
