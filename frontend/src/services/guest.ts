import { apiGet } from './api';
import type { DataEnvelope, Reservation } from '../types/api';

export function getGuestReservation(
  bookingCode: string,
  guestEmail: string,
): Promise<DataEnvelope<Reservation>> {
  return apiGet<DataEnvelope<Reservation>>(
    `/reservations/${encodeURIComponent(bookingCode)}`,
    { params: { guest_email: guestEmail } },
  );
}
