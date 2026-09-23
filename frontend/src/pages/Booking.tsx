import { useEffect, useMemo, useState, type FormEvent } from 'react';
import { Link, useSearchParams } from 'react-router-dom';
import { useLocale } from '../i18n/LocaleContext';
import { formatCurrency, formatDate } from '../i18n/format';
import { getProperties, getPropertyCabins } from '../services/catalog';
import { checkAvailability, createReservation } from '../services/booking';
import { isApiError, isBookingConflict } from '../lib/errors';
import type { Cabin, Property, Reservation } from '../types/api';

type AvailState = 'idle' | 'checking' | 'available' | 'unavailable' | 'error';
type SubmitState =
  | 'idle'
  | 'submitting'
  | 'submitted'
  | 'conflict'
  | 'validation'
  | 'error';

interface AvailSnapshot {
  cabinId: string;
  checkIn: string;
  checkOut: string;
  adults: number;
  children: number;
}

function todayLocal(): string {
  const now = new Date();
  const month = String(now.getMonth() + 1).padStart(2, '0');
  const day = String(now.getDate()).padStart(2, '0');
  return `${now.getFullYear()}-${month}-${day}`;
}

function addDays(dateStr: string, days: number): string {
  const [y, m, d] = dateStr.split('-').map(Number);
  const date = new Date(y, m - 1, d);
  date.setDate(date.getDate() + days);
  const month = String(date.getMonth() + 1).padStart(2, '0');
  const day = String(date.getDate()).padStart(2, '0');
  return `${date.getFullYear()}-${month}-${day}`;
}

/**
 * First booking step: cabin/date/guest selection, availability check,
 * guest details, reservation creation with inline confirmation.
 * Availability is informational — the reservation POST is authoritative
 * and may still return 409 on a race.
 */
export function Booking() {
  const { locale, t } = useLocale();
  const [searchParams] = useSearchParams();

  const [properties, setProperties] = useState<Property[]>([]);
  const [cabins, setCabins] = useState<Cabin[]>([]);
  const [catalogError, setCatalogError] = useState(false);
  const [queryError, setQueryError] = useState(false);

  const [propertySlug, setPropertySlug] = useState('');
  const [cabinId, setCabinId] = useState('');
  const [checkIn, setCheckIn] = useState('');
  const [checkOut, setCheckOut] = useState('');
  const [adults, setAdults] = useState(2);
  const [children, setChildren] = useState(0);

  const [availState, setAvailState] = useState<AvailState>('idle');
  const [availSnapshot, setAvailSnapshot] = useState<AvailSnapshot | null>(null);
  const [availError, setAvailError] = useState<string | null>(null);

  const [guestName, setGuestName] = useState('');
  const [guestEmail, setGuestEmail] = useState('');
  const [guestPhone, setGuestPhone] = useState('');
  const [specialRequest, setSpecialRequest] = useState('');
  const [fieldErrors, setFieldErrors] = useState<Record<string, string[]>>({});
  const [submitState, setSubmitState] = useState<SubmitState>('idle');
  const [submitError, setSubmitError] = useState<string | null>(null);
  const [reservation, setReservation] = useState<Reservation | null>(null);

  const today = useMemo(todayLocal, []);
  const selectedCabin = cabins.find((c) => c.id === cabinId) ?? null;

  function resetAvailability() {
    setAvailState('idle');
    setAvailSnapshot(null);
    setAvailError(null);
  }

  // Load active properties once.
  useEffect(() => {
    let cancelled = false;
    async function load() {
      try {
        const result = await getProperties();
        if (cancelled) return;
        setProperties(result.data);
      } catch {
        if (cancelled) return;
        setCatalogError(true);
      }
    }
    void load();
    return () => {
      cancelled = true;
    };
  }, []);

  // Resolve ?property=&cabin= preselection once the catalog is available.
  useEffect(() => {
    if (properties.length === 0) return;
    const propertyParam = searchParams.get('property');
    const cabinParam = searchParams.get('cabin');
    if (!propertyParam && !cabinParam) return;
    const matchedProperty =
      properties.find((p) => p.slug === propertyParam) ?? null;
    if (!matchedProperty) {
      setQueryError(true);
      return;
    }
    setPropertySlug(matchedProperty.slug);
    if (!cabinParam) return;
    const property = matchedProperty;
    let cancelled = false;
    async function resolveCabin() {
      try {
        const result = await getPropertyCabins(property.slug);
        if (cancelled) return;
        setCabins(result.data);
        const matchedCabin =
          result.data.find((c) => c.slug === cabinParam) ?? null;
        if (!matchedCabin) {
          setQueryError(true);
          return;
        }
        setCabinId(matchedCabin.id);
        setAvailState('idle');
        setAvailSnapshot(null);
      } catch {
        if (cancelled) return;
        setCatalogError(true);
      }
    }
    void resolveCabin();
    return () => {
      cancelled = true;
    };
    // Run once when the catalog first arrives.
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [properties.length === 0]);

  async function handlePropertyChange(slug: string) {
    setPropertySlug(slug);
    setCabinId('');
    setCabins([]);
    resetAvailability();
    if (!slug) return;
    try {
      const result = await getPropertyCabins(slug);
      setCabins(result.data);
    } catch {
      setCatalogError(true);
    }
  }

  function handleCabinChange(id: string) {
    setCabinId(id);
    resetAvailability();
  }

  function validateSearch(): string | null {
    if (!cabinId) return t('booking.selectCabinFirst');
    if (!checkIn || !checkOut) return t('booking.selectDates');
    if (checkIn < today) return t('booking.pastDate');
    if (checkOut <= checkIn) return t('booking.sameDay');
    if (!Number.isInteger(adults) || adults < 1) return t('booking.invalidAdults');
    if (!Number.isInteger(children) || children < 0)
      return t('booking.invalidChildren');
    if (selectedCabin) {
      if (adults > selectedCabin.max_adults || children > selectedCabin.max_children)
        return t('booking.capacityExceeded');
    }
    return null;
  }

  async function handleCheckAvailability(event: FormEvent) {
    event.preventDefault();
    const validationError = validateSearch();
    if (validationError) {
      setAvailError(validationError);
      setAvailState('error');
      return;
    }
    setAvailState('checking');
    setAvailError(null);
    try {
      const result = await checkAvailability({ cabinId, checkIn, checkOut });
      setAvailSnapshot({ cabinId, checkIn, checkOut, adults, children });
      setAvailState(result.data.available ? 'available' : 'unavailable');
    } catch (err) {
      setAvailSnapshot(null);
      setAvailState('error');
      setAvailError(
        isApiError(err) && err.message
          ? err.message
          : t('booking.availabilityError'),
      );
    }
  }

  const snapshotMatches =
    availState === 'available' &&
    availSnapshot !== null &&
    availSnapshot.cabinId === cabinId &&
    availSnapshot.checkIn === checkIn &&
    availSnapshot.checkOut === checkOut &&
    availSnapshot.adults === adults &&
    availSnapshot.children === children;

  function fieldError(field: string): string | undefined {
    return fieldErrors[field]?.[0];
  }

  async function handleSubmit(event: FormEvent) {
    event.preventDefault();
    if (!snapshotMatches || !selectedCabin) return;
    if (!guestName.trim() || !guestEmail.trim()) {
      setSubmitState('validation');
      setSubmitError(t('booking.guestRequired'));
      return;
    }
    setSubmitState('submitting');
    setSubmitError(null);
    setFieldErrors({});
    const idempotencyKey = crypto.randomUUID();
    try {
      const result = await createReservation(
        {
          cabinId,
          checkIn,
          checkOut,
          adults,
          children,
          guestName: guestName.trim(),
          guestEmail: guestEmail.trim(),
          ...(guestPhone.trim() ? { guestPhone: guestPhone.trim() } : {}),
          ...(specialRequest.trim()
            ? { specialRequest: specialRequest.trim() }
            : {}),
        },
        { idempotencyKey },
      );
      setReservation(result.data);
      setSubmitState('submitted');
    } catch (err) {
      if (isBookingConflict(err)) {
        setSubmitState('conflict');
        setAvailState('idle');
        setAvailSnapshot(null);
        return;
      }
      if (isApiError(err)) {
        if (err.status === 422 && err.errors) {
          setFieldErrors(mapReservationErrors(err.errors));
          const general = err.errors['general']?.[0] ?? err.errors['cabin_id']?.[0];
          setSubmitError(general ?? t('booking.submitValidation'));
          setSubmitState('validation');
          return;
        }
        if (err.status === 429) {
          setSubmitError(
            err.retryAfter !== undefined
              ? t('booking.rateLimited', { seconds: err.retryAfter })
              : t('booking.rateLimitedNoRetry'),
          );
          setSubmitState('error');
          return;
        }
        if (err.status === 0) {
          setSubmitError(t('booking.networkError'));
          setSubmitState('error');
          return;
        }
      }
      setSubmitError(t('booking.submitError'));
      setSubmitState('error');
    }
  }

  if (submitState === 'submitted' && reservation) {
    return (
      <main className="min-h-svh bg-neutral-50 font-sans text-neutral-950 antialiased">
        <div className="mx-auto max-w-2xl px-6 py-24 text-center">
          <h1 className="font-display text-4xl text-forest-800">
            {t('booking.successTitle')}
          </h1>
          <p className="mt-4 text-neutral-700">{t('booking.successBody')}</p>
          <dl className="mx-auto mt-8 max-w-md rounded-xl border border-neutral-300 bg-white p-6 text-left">
            <div className="flex justify-between gap-4">
              <dt className="text-neutral-700">{t('booking.bookingCode')}</dt>
              <dd className="font-medium">{reservation.booking_code}</dd>
            </div>
            <div className="mt-3 flex justify-between gap-4">
              <dt className="text-neutral-700">{t('booking.guestEmailLabel')}</dt>
              <dd className="font-medium">{reservation.guest_email}</dd>
            </div>
            <div className="mt-3 flex justify-between gap-4">
              <dt className="text-neutral-700">{t('booking.totalLabel')}</dt>
              <dd className="font-medium">{reservation.total}</dd>
            </div>
          </dl>
          <p className="mt-6 text-sm text-neutral-700">{t('booking.nextSteps')}</p>
          <p className="mt-8">
            <Link
              to="/"
              className="rounded-lg bg-forest-700 px-5 py-3 text-sm text-white"
            >
              {t('common.backHome')}
            </Link>
          </p>
        </div>
      </main>
    );
  }

  const estimatedNights =
    checkIn && checkOut && checkOut > checkIn
      ? Math.round(
          (new Date(`${checkOut}T00:00:00`).getTime() -
            new Date(`${checkIn}T00:00:00`).getTime()) /
            86400000,
        )
      : 0;

  return (
    <main className="min-h-svh bg-neutral-50 font-sans text-neutral-950 antialiased">
      <div className="mx-auto max-w-5xl px-6 py-16">
        <h1 className="font-display text-4xl text-forest-800">
          {t('booking.title')}
        </h1>

        {catalogError ? (
          <p role="alert" className="mt-6 rounded-xl bg-error-bg p-4 text-error">
            {t('booking.catalogError')}
          </p>
        ) : null}
        {queryError ? (
          <p role="alert" className="mt-6 rounded-xl bg-warning-bg p-4 text-warning">
            {t('booking.invalidQuery')}
          </p>
        ) : null}

        <div className="mt-10 grid gap-10 lg:grid-cols-[3fr_2fr]">
          <div>
            <form onSubmit={handleCheckAvailability} noValidate>
              <fieldset>
                <legend className="font-display text-2xl text-forest-800">
                  {t('booking.stayDetails')}
                </legend>
                <div className="mt-4">
                  <label htmlFor="booking-property">{t('booking.property')}</label>
                  <select
                    id="booking-property"
                    value={propertySlug}
                    onChange={(e) => void handlePropertyChange(e.target.value)}
                    className="mt-1 w-full rounded-lg border border-neutral-300 bg-white px-4 py-3"
                  >
                    <option value="">{t('booking.selectProperty')}</option>
                    {properties.map((property) => (
                      <option key={property.id} value={property.slug}>
                        {property.name}
                      </option>
                    ))}
                  </select>
                </div>
                <div className="mt-4">
                  <label htmlFor="booking-cabin">{t('booking.cabin')}</label>
                  <select
                    id="booking-cabin"
                    value={cabinId}
                    disabled={!propertySlug}
                    onChange={(e) => handleCabinChange(e.target.value)}
                    className="mt-1 w-full rounded-lg border border-neutral-300 bg-white px-4 py-3 disabled:opacity-60"
                  >
                    <option value="">{t('booking.selectCabin')}</option>
                    {cabins.map((cabin) => (
                      <option key={cabin.id} value={cabin.id}>
                        {cabin.name}
                      </option>
                    ))}
                  </select>
                </div>
                <div className="mt-4 grid gap-4 sm:grid-cols-2">
                  <div>
                    <label htmlFor="booking-checkin">{t('booking.checkIn')}</label>
                    <input
                      id="booking-checkin"
                      type="date"
                      required
                      min={today}
                      value={checkIn}
                      onChange={(e) => {
                        setCheckIn(e.target.value);
                        resetAvailability();
                      }}
                      className="mt-1 w-full rounded-lg border border-neutral-300 bg-white px-4 py-3"
                    />
                  </div>
                  <div>
                    <label htmlFor="booking-checkout">{t('booking.checkOut')}</label>
                    <input
                      id="booking-checkout"
                      type="date"
                      required
                      min={checkIn ? addDays(checkIn, 1) : addDays(today, 1)}
                      value={checkOut}
                      onChange={(e) => {
                        setCheckOut(e.target.value);
                        resetAvailability();
                      }}
                      className="mt-1 w-full rounded-lg border border-neutral-300 bg-white px-4 py-3"
                    />
                  </div>
                </div>
                <div className="mt-4 grid gap-4 sm:grid-cols-2">
                  <div>
                    <label htmlFor="booking-adults">{t('booking.adults')}</label>
                    <input
                      id="booking-adults"
                      type="number"
                      required
                      min={1}
                      max={selectedCabin?.max_adults}
                      value={adults}
                      onChange={(e) => {
                        setAdults(Number(e.target.value));
                        resetAvailability();
                      }}
                      className="mt-1 w-full rounded-lg border border-neutral-300 bg-white px-4 py-3"
                    />
                  </div>
                  <div>
                    <label htmlFor="booking-children">{t('booking.children')}</label>
                    <input
                      id="booking-children"
                      type="number"
                      required
                      min={0}
                      max={selectedCabin?.max_children}
                      value={children}
                      onChange={(e) => {
                        setChildren(Number(e.target.value));
                        resetAvailability();
                      }}
                      className="mt-1 w-full rounded-lg border border-neutral-300 bg-white px-4 py-3"
                    />
                  </div>
                </div>
                <p className="mt-6">
                  <button
                    type="submit"
                    disabled={availState === 'checking'}
                    aria-busy={availState === 'checking'}
                    className="rounded-lg bg-forest-700 px-6 py-3 text-sm text-white disabled:opacity-60"
                  >
                    {availState === 'checking'
                      ? t('booking.checking')
                      : t('booking.checkAvailability')}
                  </button>
                </p>
              </fieldset>
            </form>

            <div aria-live="polite" className="mt-6">
              {availState === 'available' ? (
                <p className="rounded-xl bg-success-bg p-4 text-success">
                  <span aria-hidden="true">✓ </span>
                  {t('booking.available')}
                </p>
              ) : null}
              {availState === 'unavailable' ? (
                <div className="rounded-xl bg-warning-bg p-4">
                  <p className="font-medium text-warning">
                    <span aria-hidden="true">! </span>
                    {t('booking.unavailable')}
                  </p>
                  <p className="mt-2 text-sm text-neutral-700">
                    {t('booking.unavailableHint')}
                  </p>
                </div>
              ) : null}
              {availState === 'error' && availError ? (
                <p role="alert" className="rounded-xl bg-error-bg p-4 text-error">
                  {availError}
                </p>
              ) : null}
            </div>

            {submitState === 'conflict' ? (
              <div role="alert" className="mt-6 rounded-xl bg-warning-bg p-4">
                <p className="font-medium text-warning">
                  {t('booking.conflictTitle')}
                </p>
                <p className="mt-2 text-sm text-neutral-700">
                  {t('booking.conflictBody')}
                </p>
              </div>
            ) : null}

            {snapshotMatches ? (
              <form onSubmit={handleSubmit} noValidate className="mt-10">
                <fieldset>
                  <legend className="font-display text-2xl text-forest-800">
                    {t('booking.guestDetails')}
                  </legend>
                  <p className="mt-2 text-sm text-neutral-700">
                    {t('booking.guestTiedToEmail')}
                  </p>
                  <div className="mt-4">
                    <label htmlFor="booking-name">{t('booking.guestName')}</label>
                    <input
                      id="booking-name"
                      type="text"
                      autoComplete="name"
                      required
                      value={guestName}
                      onChange={(e) => setGuestName(e.target.value)}
                      aria-describedby={
                        fieldError('guest_name') ? 'booking-name-error' : undefined
                      }
                      className="mt-1 w-full rounded-lg border border-neutral-300 bg-white px-4 py-3"
                    />
                    {fieldError('guest_name') ? (
                      <p id="booking-name-error" className="mt-1 text-sm text-error">
                        {fieldError('guest_name')}
                      </p>
                    ) : null}
                  </div>
                  <div className="mt-4">
                    <label htmlFor="booking-email">{t('booking.guestEmail')}</label>
                    <input
                      id="booking-email"
                      type="email"
                      autoComplete="email"
                      required
                      value={guestEmail}
                      onChange={(e) => setGuestEmail(e.target.value)}
                      aria-describedby={
                        fieldError('guest_email') ? 'booking-email-error' : undefined
                      }
                      className="mt-1 w-full rounded-lg border border-neutral-300 bg-white px-4 py-3"
                    />
                    {fieldError('guest_email') ? (
                      <p id="booking-email-error" className="mt-1 text-sm text-error">
                        {fieldError('guest_email')}
                      </p>
                    ) : null}
                  </div>
                  <div className="mt-4">
                    <label htmlFor="booking-phone">{t('booking.guestPhone')}</label>
                    <input
                      id="booking-phone"
                      type="tel"
                      autoComplete="tel"
                      value={guestPhone}
                      onChange={(e) => setGuestPhone(e.target.value)}
                      aria-describedby={
                        fieldError('guest_phone') ? 'booking-phone-error' : undefined
                      }
                      className="mt-1 w-full rounded-lg border border-neutral-300 bg-white px-4 py-3"
                    />
                    {fieldError('guest_phone') ? (
                      <p id="booking-phone-error" className="mt-1 text-sm text-error">
                        {fieldError('guest_phone')}
                      </p>
                    ) : null}
                  </div>
                  <div className="mt-4">
                    <label htmlFor="booking-request">{t('booking.specialRequest')}</label>
                    <textarea
                      id="booking-request"
                      rows={3}
                      value={specialRequest}
                      onChange={(e) => setSpecialRequest(e.target.value)}
                      aria-describedby={
                        fieldError('special_request')
                          ? 'booking-request-error'
                          : undefined
                      }
                      className="mt-1 w-full rounded-lg border border-neutral-300 bg-white px-4 py-3"
                    />
                    {fieldError('special_request') ? (
                      <p id="booking-request-error" className="mt-1 text-sm text-error">
                        {fieldError('special_request')}
                      </p>
                    ) : null}
                  </div>
                  {submitState === 'validation' && submitError ? (
                    <p role="alert" className="mt-6 rounded-xl bg-error-bg p-4 text-error">
                      {submitError}
                    </p>
                  ) : null}
                  {submitState === 'error' && submitError ? (
                    <p role="alert" className="mt-6 rounded-xl bg-error-bg p-4 text-error">
                      {submitError}
                    </p>
                  ) : null}
                  <p className="mt-6">
                    <button
                      type="submit"
                      disabled={submitState === 'submitting'}
                      aria-busy={submitState === 'submitting'}
                      className="rounded-lg bg-accent-600 px-6 py-3 text-sm text-white disabled:opacity-60"
                    >
                      {submitState === 'submitting'
                        ? t('booking.submitting')
                        : t('booking.submit')}
                    </button>
                  </p>
                </fieldset>
              </form>
            ) : null}
          </div>

          <aside aria-label={t('booking.summary')} className="lg:pt-[52px]">
            <div className="rounded-xl border border-neutral-300 bg-white p-6">
              <h2 className="font-display text-2xl text-forest-800">
                {t('booking.summary')}
              </h2>
              <dl className="mt-4 space-y-3 text-sm">
                <div className="flex justify-between gap-4">
                  <dt className="text-neutral-700">{t('booking.cabin')}</dt>
                  <dd className="text-right font-medium">
                    {selectedCabin ? selectedCabin.name : '—'}
                  </dd>
                </div>
                <div className="flex justify-between gap-4">
                  <dt className="text-neutral-700">{t('booking.checkIn')}</dt>
                  <dd className="text-right font-medium">
                    {checkIn ? formatDate(locale, checkIn) : '—'}
                  </dd>
                </div>
                <div className="flex justify-between gap-4">
                  <dt className="text-neutral-700">{t('booking.checkOut')}</dt>
                  <dd className="text-right font-medium">
                    {checkOut ? formatDate(locale, checkOut) : '—'}
                  </dd>
                </div>
                <div className="flex justify-between gap-4">
                  <dt className="text-neutral-700">{t('booking.nights')}</dt>
                  <dd className="text-right font-medium">{estimatedNights || '—'}</dd>
                </div>
                <div className="flex justify-between gap-4">
                  <dt className="text-neutral-700">{t('booking.guests')}</dt>
                  <dd className="text-right font-medium">
                    {t('booking.guestCount', { adults, children })}
                  </dd>
                </div>
                <div className="flex justify-between gap-4">
                  <dt className="text-neutral-700">{t('booking.estimate')}</dt>
                  <dd className="text-right font-medium">
                    {selectedCabin
                      ? formatCurrency(
                          locale,
                          selectedCabin.base_price,
                          selectedCabin.currency,
                        )
                      : '—'}
                  </dd>
                </div>
              </dl>
              <p className="mt-4 text-xs text-neutral-700">
                {t('booking.estimateNote')}
              </p>
            </div>
          </aside>
        </div>
      </div>
    </main>
  );

}

function mapReservationErrors(
  errors: Record<string, string[]>,
): Record<string, string[]> {
  const mapped: Record<string, string[]> = {};
  const fieldMap: Record<string, string> = {
    cabin_id: 'cabin',
    check_in: 'checkIn',
    check_out: 'checkOut',
    adults: 'adults',
    children: 'children',
    guest_name: 'guest_name',
    guest_email: 'guest_email',
    guest_phone: 'guest_phone',
    special_request: 'special_request',
  };
  for (const [field, messages] of Object.entries(errors)) {
    mapped[fieldMap[field] ?? field] = messages;
  }
  return mapped;
}
