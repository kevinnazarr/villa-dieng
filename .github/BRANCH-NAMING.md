# Branch Naming Quick Reference

A quick reference for choosing the correct branch name.

## Rule

Use:

```text
<type>/<short-description>
```

Use lowercase and hyphens. Keep the description short and specific.

## Types

| Type | Use for | Example |
|---|---|---|
| `feature/` | New functionality | `feature/reservation` |
| `fix/` | Bug fixes | `fix/booking-conflict` |
| `chore/` | Setup, tooling, dependencies, CI | `chore/project-foundation` |
| `docs/` | Documentation-only changes | `docs/update-technical-design` |
| `refactor/` | Code restructuring without intended behavior change | `refactor/reservation-domain` |
| `test/` | Test-focused changes | `test/reservation-concurrency` |

## Recommended Names for This Project

### Foundation

```text
chore/project-foundation
chore/docker-setup
chore/ci-setup
```

### Frontend

```text
feature/frontend-foundation
feature/home-page
feature/cabin-detail
feature/booking-checkout
feature/customer-account
feature/admin-dashboard
```

### Backend

```text
feature/backend-foundation
feature/availability
feature/reservation
feature/payment
feature/expiration-worker
```

### Bug Fixes

```text
fix/booking-conflict
fix/availability-query
fix/checkout-validation
fix/payment-status
```

### Documentation

```text
docs/update-technical-design
docs/add-booking-rules
docs/update-api-documentation
```

## What Not to Do

Avoid vague names:

```text
feature/update
feature/new-stuff
fix/bug
dev
work
test-branch
```

Avoid putting too many unrelated tasks into one branch:

```text
feature/everything
```

Prefer one logical change per branch.

## Simple Decision

Ask:

1. Am I adding functionality? → `feature/`
2. Am I fixing existing behavior? → `fix/`
3. Am I doing setup/tooling/maintenance? → `chore/`
4. Am I only changing documentation? → `docs/`
5. Am I restructuring code without changing behavior? → `refactor/`
6. Am I primarily adding/changing tests? → `test/`
