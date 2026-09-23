/**
 * Locale model. Matches the backend `users.locale` allowlist
 * (`CHECK (locale IN ('id', 'en'))`) so a future preference sync needs no
 * client-side mapping — but no sync exists yet (no endpoint accepts locale).
 */

export type Locale = 'id' | 'en';

export const DEFAULT_LOCALE: Locale = 'id';

export const LOCALE_STORAGE_KEY = 'villa_dieng_locale';

export function isLocale(value: unknown): value is Locale {
  return value === 'id' || value === 'en';
}
