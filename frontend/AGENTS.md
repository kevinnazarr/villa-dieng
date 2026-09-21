# Frontend AGENTS.md

These instructions apply to work inside `frontend/` and supplement the root `AGENTS.md`.

## Stack

The frontend uses:

- React
- Vite
- TypeScript
- Tailwind CSS v4
- shadcn/ui
- Laravel REST API
- ID/EN localization

Follow existing repository conventions before introducing new libraries or patterns.

## Architecture

Prefer a clear flow:

```text
UI
  ↓
Page / Feature
  ↓
Hook / Application Logic
  ↓
API Client / Service
  ↓
Laravel REST API
```

Use local component state by default. Do not introduce global state management unless the feature genuinely requires shared client state.

Suggested organization:

```text
src/
├── app/
├── components/
├── features/
├── hooks/
├── lib/
├── services/
├── types/
└── locales/
```

Do not reorganize the whole frontend only to match this structure.

## API Contract

Laravel is the backend source of truth.

Frontend validation improves UX but never replaces backend validation.

Always account for:

- loading
- empty
- error
- retry where appropriate
- success
- stale availability
- server-calculated totals

For booking conflicts, handle HTTP `409 BOOKING_CONFLICT` as a normal business outcome rather than retrying the booking request blindly.

Never automatically retry a reservation creation request unless the operation is explicitly designed to be idempotent.

## Booking UX

The booking flow should make these states clear:

```text
search availability
  ↓
select dates / guests
  ↓
review reservation
  ↓
submit reservation
  ↓
payment
  ↓
success / failure
```

Availability can change between viewing the calendar and submitting a reservation.

The UI must gracefully handle a conflict and allow the user to choose another available date/time or restart the affected step.

## Forms and Validation

Use the project's established form and validation patterns.

Keep validation messages clear and localized.

Do not duplicate backend business rules in a way that can drift from the API. Client-side validation should primarily prevent obviously invalid input and improve UX.

## Localization

Support:

- Indonesian (`id`)
- English (`en`)

Use locale-aware formatting for:

- dates
- numbers
- currency
- validation messages
- user-facing status labels

Do not hardcode user-facing text when it belongs to the localization system.

## Design System

Follow `docs/DESIGN.md`.

Preserve the approved visual direction:

- nature-first
- warm alpine retreat
- restrained premium feel
- natural materials
- forest green / warm cream / charcoal / terracotta direction
- DM Sans as primary typography
- Cormorant Garamond only where appropriate for editorial/hero headings

Do not introduce generic SaaS or generic hotel styling that conflicts with the design brief.

## Accessibility

Target WCAG 2.2 AA.

Check:

- keyboard navigation
- visible focus
- semantic HTML
- labels
- form errors
- dialog accessibility
- sufficient contrast
- reduced motion where relevant

## Performance

Prefer:

- small components
- appropriate lazy loading
- optimized images
- avoiding unnecessary client-side state
- avoiding unnecessary re-renders
- server-driven data where appropriate

Do not optimize prematurely.

## Security

Never place secrets in frontend code.

Treat API responses and URL/query parameters as untrusted.

Do not expose internal error details to users.

## Testing

For meaningful frontend changes, test user-visible behavior where practical.

For booking-related changes, include relevant cases such as:

- available dates
- unavailable dates
- invalid guest count
- validation errors
- `409 BOOKING_CONFLICT`
- payment failure/retry
- localization
- accessibility

## Change Logging

Meaningful frontend changes must update `docs/logs/LOG-ID.md`.

Include, when applicable:

- UI/UX changes
- pages/routes changed
- components created or modified
- API integration changes
- localization changes
- accessibility considerations
- tests and verification
- known limitations

When a durable frontend convention or design decision changes, update relevant repository knowledge and Obsidian memory when useful.
