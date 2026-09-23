import type { Locale } from './locale';

/**
 * Thin Intl wrappers. Backend money arrives as decimal strings and dates as
 * `Y-m-d`/ISO — parsing stays defensive here. No booking-specific logic
 * (night counts, breakdowns) belongs in this shell.
 */

const INTL_LOCALES: Record<Locale, string> = {
  id: 'id-ID',
  en: 'en-US',
};

export function formatDate(
  locale: Locale,
  value: string,
  options?: Intl.DateTimeFormatOptions,
): string {
  const date = new Date(value);
  if (Number.isNaN(date.getTime())) return value;
  return new Intl.DateTimeFormat(INTL_LOCALES[locale], {
    day: 'numeric',
    month: 'short',
    year: 'numeric',
    ...options,
  }).format(date);
}

export function formatNumber(locale: Locale, value: number): string {
  return new Intl.NumberFormat(INTL_LOCALES[locale]).format(value);
}

export function formatCurrency(
  locale: Locale,
  amount: string,
  currency = 'IDR',
): string {
  const parsed = Number(amount);
  if (!Number.isFinite(parsed)) return amount;
  return new Intl.NumberFormat(INTL_LOCALES[locale], {
    style: 'currency',
    currency,
    minimumFractionDigits: 0,
    maximumFractionDigits: 2,
  }).format(parsed);
}
