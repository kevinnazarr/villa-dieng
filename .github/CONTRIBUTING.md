# Development Workflow

This document defines the Git workflow for the Cabin Villa Dieng project.

## Core Workflow

Never develop directly on `main`.

Use this flow:

```text
main
  ↓
create a working branch
  ↓
code
  ↓
test / verify
  ↓
update docs/logs/LOG-ID.md
  ↓
commit
  ↓
push branch to GitHub
  ↓
review the diff / Pull Request
  ↓
merge into main
  ↓
update local main
  ↓
create the next working branch
```

## Branch Rules

1. `main` is the stable baseline.
2. Do not develop directly on `main`.
3. Start a new branch from an up-to-date `main`.
4. One branch should represent one logical change.
5. Keep branches short-lived where practical.
6. Push the branch to GitHub before creating/reviewing a Pull Request.
7. Review the changed files and diff before merging.
8. Merge only after the change has been verified.
9. After merging, update local `main` before starting the next branch.

## Branch Naming

Use lowercase names with hyphens.

### Feature

Use when adding user-facing or domain functionality.

```text
feature/<short-description>
```

Examples:

```text
feature/backend-foundation
feature/frontend-foundation
feature/reservation
feature/payment
feature/admin-dashboard
feature/booking-checkout
```

### Fix

Use when correcting an existing bug or incorrect behavior.

```text
fix/<short-description>
```

Examples:

```text
fix/booking-conflict
fix/checkout-validation
fix/payment-status
fix/availability-calendar
```

### Chore

Use for setup, tooling, dependencies, configuration, CI, or repository maintenance.

```text
chore/<short-description>
```

Examples:

```text
chore/project-foundation
chore/docker-setup
chore/update-dependencies
chore/ci-setup
```

### Docs

Use when the change is documentation-only.

```text
docs/<short-description>
```

Examples:

```text
docs/update-technical-design
docs/add-booking-rules
docs/update-api-documentation
```

### Refactor

Use when restructuring existing code without intentionally changing behavior.

```text
refactor/<short-description>
```

Examples:

```text
refactor/reservation-domain
refactor/api-client
refactor/frontend-components
```

### Test

Use for test-focused changes.

```text
test/<short-description>
```

Examples:

```text
test/reservation-concurrency
test/payment-flow
test/availability
```

## Commit Convention

Prefer Conventional Commits:

```text
feat: ...
fix: ...
chore: ...
docs: ...
refactor: ...
test: ...
perf: ...
```

Examples:

```text
feat: implement reservation creation
fix: handle overlapping reservation conflict
chore: establish project foundation
docs: update booking architecture
refactor: simplify reservation action
test: add reservation concurrency coverage
perf: optimize availability query
```

## Pull Request

Before merging a branch into `main`, verify:

- The change has a clear purpose.
- Unrelated changes are not included.
- Tests relevant to the change have been run.
- Manual verification has been performed when appropriate.
- `docs/logs/LOG-ID.md` has been updated for meaningful changes.
- Database/API changes are documented when applicable.
- Known limitations or follow-up work are documented.

## Change Log

Every meaningful implementation change should have a corresponding file:

```text
docs/logs/LOG-ID.md
```

Use sequential IDs when practical:

```text
LOG-001.md
LOG-002.md
LOG-003.md
```

The change log records what was actually changed and verified. It is not a replacement for Git history.

## Typical Branch Lifecycle

Example:

```text
main
  ↓
git switch main
git pull origin main
  ↓
git switch -c feature/reservation
  ↓
development
  ↓
tests
  ↓
docs/logs/LOG-005.md
  ↓
git add .
git commit -m "feat: implement reservation creation"
git push -u origin feature/reservation
  ↓
GitHub Pull Request
  ↓
merge → main
  ↓
git switch main
git pull origin main
  ↓
create the next branch
```
