# Backend AGENTS.md

These instructions apply to work inside `backend/` and supplement the root `AGENTS.md`.

## Stack

The backend uses:

- Laravel
- PHP
- PostgreSQL
- Redis
- REST API
- Docker
- sandbox payment simulation
- mail trap/log simulation

Follow existing repository conventions before introducing new packages or architectural patterns.

## Architecture

Prefer a clear application flow:

```text
HTTP Controller
  ↓
Form Request / Authentication
  ↓
Action / Application Service
  ↓
Domain Rules / State Machine
  ↓
Eloquent / Repository
  ↓
PostgreSQL
```

Suggested organization:

```text
app/
├── Actions/
├── Domain/
├── Http/
├── Jobs/
├── Models/
├── Policies/
└── Services/
```

Do not reorganize the entire backend merely to match this structure.

## PostgreSQL Is the Availability Authority

Availability must be enforced by PostgreSQL.

Redis may be used for:

- caching
- queues
- rate limiting
- non-authoritative distributed coordination

Redis must not become the primary source of truth for inventory availability.

## Anti-Double-Booking

For reservation creation:

1. Start a database transaction.
2. Lock the relevant cabin when appropriate.
3. Re-check availability inside the transaction.
4. Insert the reservation.
5. Preserve the PostgreSQL exclusion constraint as final protection.
6. Catch and map a constraint conflict to HTTP `409 BOOKING_CONFLICT`.
7. Commit only when the transaction is valid.

Do not rely on a Redis lock as the primary anti-double-booking mechanism.

The critical invariant is:

> A cabin must not have more than one inventory-blocking reservation overlapping the same dates at the same time.

Blocking statuses:

```text
pending_payment
paid
confirmed
```

Reservation ranges use `[)` semantics:

```text
check_in <= date < check_out
```

Therefore a reservation ending on the same day another reservation starts does not overlap.

## Reservation State Machine

Allowed reservation states:

```text
pending_payment
    ├── paid
    ├── expired
    └── cancelled

paid
    ├── confirmed
    └── cancelled

confirmed
    └── cancelled
```

Do not add or bypass state transitions without documenting the domain change.

## Payment State Machine

Expected payment states:

```text
pending
  ↓
processing
  ├── paid
  ├── failed
  └── unknown

unknown
  ├── paid
  └── failed

failed
  └── processing
```

Payment operations should be idempotent where the provider workflow requires it.

Use unique provider references and request/idempotency identifiers where appropriate.

## Reservation Events

Reservation events are append-oriented audit records.

Meaningful lifecycle changes should produce appropriate events such as:

```text
created
payment_initiated
payment_succeeded
payment_failed
confirmed
expired
cancelled
availability_blocked
availability_unblocked
```

Do not silently delete or rewrite historical event records unless explicitly required.

## Price Snapshot

At reservation creation, store the authoritative price snapshot:

- nightly rate
- subtotal
- total
- currency

Later catalog/pricing changes must not silently alter an existing reservation's stored financial snapshot.

## Database Conventions

Follow the technical design for:

- UUID identifiers
- `NUMERIC(12,2)` monetary values
- `DATE` for stay dates
- timezone-aware timestamps where appropriate
- `CITEXT` for case-insensitive email identity where configured
- `JSONB` for structured metadata where appropriate
- explicit foreign keys
- database-level checks and constraints
- indexes based on actual query patterns

Do not weaken database constraints to make application code easier.

## API

Use the established `/api/v1` boundary.

Preserve stable response/error semantics.

Relevant public/customer areas include:

- property
- cabin
- availability
- reservation
- payment
- authentication
- customer account/reservations

Admin endpoints must enforce authorization and policies.

## Validation and Authorization

Validate external input server-side.

Use Form Requests or the established validation pattern.

Authorization must be enforced on the server. Never rely on frontend route visibility for access control.

## Expiration

Pending reservations with expiration timestamps must be handled by the expiration worker/job according to the technical design.

When expiring a reservation:

1. Find eligible pending reservations.
2. Lock the reservation.
3. Re-check the status.
4. Change to `expired` only if still eligible.
5. Append the appropriate event.

## Security

Never commit:

- database credentials
- API keys
- JWT secrets
- payment secrets
- production customer data
- real payment information
- private tokens

Do not log secrets or sensitive payment/customer data.

Use appropriate authorization, validation, rate limiting, and safe error responses.

## Testing

Backend tests should cover the business rules, not only controller status codes.

For reservation and availability changes, include relevant coverage for:

- valid reservation
- invalid dates
- invalid guest count
- unavailable dates
- overlapping reservations
- concurrent booking attempts
- exclusion constraint conflict
- state transitions
- expiration
- payment failure/retry
- idempotency

For concurrency-sensitive behavior, prefer an integration/feature test that exercises the actual database constraint and transaction behavior.

## Change Logging

Meaningful backend changes must update `docs/logs/LOG-ID.md`.

Backend logs should explicitly document, when applicable:

- domain/business changes
- API changes
- database/schema changes
- reservation/payment state changes
- availability/concurrency implications
- security implications
- jobs/queues/cache changes
- tests and verification
- known limitations

For reservation, payment, or availability changes, explicitly state whether the relevant invariants and concurrency protections were verified.

When a durable backend architecture or domain decision changes, update relevant repository knowledge and Obsidian memory when useful.
