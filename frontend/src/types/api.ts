/**
 * Frontend API types, derived 1:1 from the Laravel API Resources
 * (`backend/app/Http/Resources/Api/V1/*`). Resource output — not database
 * columns — is the source of truth. Money (`decimal:2`) arrives as strings;
 * all domain ids are UUID strings; booking codes are separate opaque strings.
 */

export type UUID = string;

export type DateString = string;
export type DateTimeString = string;

export type ReservationStatus =
  | 'pending_payment'
  | 'paid'
  | 'confirmed'
  | 'expired'
  | 'cancelled';

export type PaymentStatus =
  | 'pending'
  | 'processing'
  | 'paid'
  | 'failed'
  | 'unknown'
  | 'expired';

export type ReservationEventType =
  | 'created'
  | 'payment_initiated'
  | 'payment_succeeded'
  | 'payment_failed'
  | 'confirmed'
  | 'expired'
  | 'cancelled'
  | 'availability_blocked'
  | 'availability_unblocked';

export type UserRole = 'guest' | 'admin';

export type PaymentScenario = 'success' | 'fail' | 'timeout';

export interface User {
  id: UUID;
  name: string;
  email: string;
  phone: string | null;
  locale: string;
  role: UserRole;
}

export interface Property {
  id: UUID;
  name: string;
  slug: string;
  description: string | null;
  timezone: string;
  currency: string;
  is_active: boolean;
}

export interface CabinImage {
  id: UUID;
  path: string;
  alt_text: string | null;
  sort_order: number;
  is_primary: boolean;
}

export interface Amenity {
  id: UUID;
  name: string;
  slug: string;
  icon: string | null;
}

export interface Cabin {
  id: UUID;
  property_id: UUID;
  name: string;
  slug: string;
  description: string | null;
  max_adults: number;
  max_children: number;
  base_price: string;
  currency: string;
  is_active: boolean;
  images?: CabinImage[];
  amenities?: Amenity[];
  property?: Property | null;
}

export interface Payment {
  id: UUID;
  reservation_id: UUID;
  provider: string;
  amount: string;
  currency: string;
  status: PaymentStatus;
  paid_at: DateTimeString | null;
  expires_at: DateTimeString | null;
}

export interface ReservationEvent {
  id: UUID;
  event_type: ReservationEventType;
  from_status: ReservationStatus | null;
  to_status: ReservationStatus | null;
  created_at: DateTimeString;
}

export interface Reservation {
  id: UUID;
  booking_code: string;
  cabin_id: UUID;
  check_in: DateString;
  check_out: DateString;
  adults: number;
  children: number;
  nightly_rate: string;
  subtotal: string;
  total: string;
  currency: string;
  status: ReservationStatus;
  guest_name: string;
  guest_email: string;
  guest_phone: string | null;
  special_request: string | null;
  expires_at: DateTimeString | null;
  cabin?: Cabin | null;
  payments?: Payment[];
  events?: ReservationEvent[];
}

export interface AvailabilityBlock {
  id: UUID;
  cabin_id: UUID;
  starts_on: DateString;
  ends_on: DateString;
  reason: string;
}

export interface AvailabilityResult {
  cabin_id: UUID;
  check_in: DateString;
  check_out: DateString;
  available: boolean;
}

export interface AuthPayload {
  user: User;
  token: string;
}

export interface DataEnvelope<T> {
  data: T;
}

export interface CollectionEnvelope<T> {
  data: T[];
}

export interface PaginatedResponse<T> {
  data: T[];
  links: {
    first: string | null;
    last: string | null;
    prev: string | null;
    next: string | null;
  };
  meta: {
    current_page: number;
    from: number | null;
    last_page: number;
    per_page: number;
    to: number | null;
    total: number;
    path: string;
  };
}

export interface HealthResponse {
  status: 'ok' | 'degraded';
  checks: {
    database: 'ok' | 'down';
    redis: 'ok' | 'down';
  };
}
