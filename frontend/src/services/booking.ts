import { apiGet, apiPost, type ApiRequestOptions } from './api';
import type {
  AvailabilityResult,
  DataEnvelope,
  Reservation,
} from '../types/api';

export interface AvailabilityQuery {
  cabinId: string;
  checkIn: string;
  checkOut: string;
}

export interface CreateReservationInput {
  cabinId: string;
  checkIn: string;
  checkOut: string;
  adults: number;
  children: number;
  guestName: string;
  guestEmail: string;
  guestPhone?: string;
  specialRequest?: string;
}

/**
 * Booking reads/writes. Thin wrappers around the shared client — no caching,
 * no state. Availability is informational only; the reservation POST is
 * authoritative and may still return 409 on a race.
 */
export function checkAvailability(
  query: AvailabilityQuery,
): Promise<DataEnvelope<AvailabilityResult>> {
  return apiGet<DataEnvelope<AvailabilityResult>>('/availability', {
    params: {
      cabin_id: query.cabinId,
      check_in: query.checkIn,
      check_out: query.checkOut,
    },
  });
}

export function createReservation(
  input: CreateReservationInput,
  options?: ApiRequestOptions,
): Promise<DataEnvelope<Reservation>> {
  return apiPost<DataEnvelope<Reservation>>(
    '/reservations',
    {
      cabin_id: input.cabinId,
      check_in: input.checkIn,
      check_out: input.checkOut,
      adults: input.adults,
      children: input.children,
      guest_name: input.guestName,
      guest_email: input.guestEmail,
      ...(input.guestPhone ? { guest_phone: input.guestPhone } : {}),
      ...(input.specialRequest ? { special_request: input.specialRequest } : {}),
    },
    options,
  );
}
