# AGENTS.md

Behavioral and project-specific instructions for AI coding agents working on the Cabin Villa Dieng Booking Engine.

## 1. Project Context

This repository contains the Cabin Villa Dieng Booking Engine: a production-ready villa profile and self-service booking engine for an exclusive cabin-style villa in Dieng, Wonosobo.

Primary project documents:

- `docs/PRD.md` — product requirements and MVP scope.
- `docs/DESIGN.md` — visual and UX direction.
- `docs/Technical-Design-Villa-Dieng.md` — technical architecture, database, API, booking concurrency, and state machines.
- `docs/knowledge/` — durable project knowledge and architectural decisions.
- `docs/logs/` — implementation/change history.

If a referenced document is missing, do not invent its contents.

## 2. Instruction Hierarchy

Apply instructions in this order:

1. System/developer instructions.
2. This root `AGENTS.md`.
3. The nearest nested `AGENTS.md`.
4. Existing repository conventions.
5. Task-specific user requirements.

More specific nested instructions refine, rather than casually replace, the global rules.

## 3. Think Before Coding

Before implementing:

- Understand the requested outcome and define success criteria.
- Inspect relevant existing code and documentation.
- Do not assume missing requirements.
- Surface meaningful ambiguity or trade-offs before making a consequential choice.
- Prefer the simplest solution that satisfies the requirement.
- For multi-step work, make a short plan and verify each meaningful step.

Do not add speculative features, abstractions, configuration, or error handling without a concrete need.

## 4. Simplicity First

Prefer:

- Existing project patterns over new patterns.
- Small, direct changes over broad abstractions.
- Reuse over duplication.
- Explicit behavior over unnecessary configurability.

Do not over-engineer for hypothetical future requirements.

If a solution can be substantially simpler without sacrificing correctness, use the simpler solution.

## 5. Surgical Changes

Keep changes focused:

- Touch only files required by the task.
- Do not refactor unrelated code.
- Do not rewrite working code merely for stylistic preference.
- Match existing naming, formatting, and architectural conventions.
- Clean up only orphaned code created by your own change unless broader cleanup is explicitly requested.

## 6. Project Source of Truth

The Git repository is the canonical source of truth for the implemented project.

Use the documents according to their roles:

- `PRD.md` → what the product should do.
- `DESIGN.md` → how the product should look and behave visually.
- `Technical-Design-Villa-Dieng.md` → how the system is designed technically.
- `docs/knowledge/` → durable decisions, domain knowledge, conventions, and troubleshooting knowledge.
- Code/tests → what is actually implemented and verified.
- `docs/logs/LOG-ID.md` → canonical history of meaningful implementation changes.

Do not silently contradict project documentation. If implementation requires a genuine architectural or product change, document it explicitly.

## 7. Obsidian Project Memory

This project uses a local Obsidian vault as a project knowledge and memory workspace.

### Vault identity

The Obsidian vault name should match the project name:

`villa-dieng`

The vault is stored separately from the Git repository.

The local Obsidian path is managed by the configured Obsidian MCP. Never hardcode or guess a machine-specific absolute path when using the Obsidian integration.

### Vault initialization

When the configured Obsidian MCP is available:

- Check whether the project vault already exists before creating project structure.
- If it does not exist, initialize it.
- If it exists, inspect and reuse the existing structure.
- Do not recreate or overwrite existing notes merely because the agent starts a new task.

Recommended vault structure:

```text
00-Project/
01-Architecture/
02-Domain/
03-Decisions/
04-Development-Logs/
05-Troubleshooting/
06-References/
```

Create only the folders/notes that are useful for the current project stage. Do not generate large amounts of empty documentation.

### Memory rules

Before making an architectural, domain, integration, or convention decision:

1. Check relevant Obsidian project memory when available.
2. Check the repository documentation.
3. Prefer an existing decision over creating duplicate knowledge.
4. Update an existing note when the knowledge already exists.
5. Create new memory only when the information is durable and useful.

Obsidian is not a replacement for committed project documentation.

Do not store in Obsidian:

- passwords
- API keys
- access tokens
- database credentials
- payment secrets
- private customer data
- other sensitive credentials
- temporary chain-of-thought or private reasoning
- raw chat transcripts

### Knowledge synchronization

Do not blindly mirror the entire Git repository into Obsidian.

Maintain useful project knowledge instead:

- Architecture and system relationships → `01-Architecture/`
- Domain rules → `02-Domain/`
- Architectural decisions → `03-Decisions/`
- Meaningful implementation history → `04-Development-Logs/`
- Recurring technical problems and solutions → `05-Troubleshooting/`
- External references and integration notes → `06-References/`

The repository remains canonical. Obsidian should provide a navigable knowledge layer around it.

## 8. Change Logging

Every meaningful implementation change MUST produce a change log under:

`docs/logs/LOG-ID.md`

Use sequential IDs when practical:

```text
LOG-001.md
LOG-002.md
LOG-003.md
```

A meaningful log should contain:

- Log ID.
- Date.
- Change type.
- Status.
- Summary.
- Changes made.
- Files changed.
- Database changes, if any.
- API changes, if any.
- Important technical decisions.
- Tests and verification performed.
- Remaining issues or follow-up work.

Never claim a test or verification was performed if it was not actually run.

A corresponding Obsidian development note may be created under `04-Development-Logs/` when useful. The repository `docs/logs/LOG-ID.md` remains the canonical project history.

Do not put secrets, credentials, tokens, passwords, private customer data, or other sensitive information into logs.

## 9. Git Workflow

Follow the detailed repository workflow in:

`.github/CONTRIBUTING.md`

Core rule:

- Do not develop directly on `main`.
- Start work from an up-to-date `main`.
- Use one branch for one logical change.
- Verify the change before committing.
- Update the appropriate `LOG-ID.md`.
- Push the working branch to GitHub.
- Review the diff/Pull Request.
- Merge into `main`.
- Update local `main` before creating the next working branch.

Recommended branch categories:

```text
feature/<short-description>
fix/<short-description>
chore/<short-description>
docs/<short-description>
refactor/<short-description>
test/<short-description>
```

## 10. Security Baseline

Never commit or expose:

- API keys
- passwords
- database credentials
- JWT secrets
- payment credentials
- private tokens
- production customer data
- real payment information
- `.env` secrets

Treat all external input as untrusted.

Validate and authorize on the server. Frontend validation is for user experience and does not replace backend validation.

## 11. Database and Booking Safety

For reservation, availability, or payment changes:

- Read the technical design before implementation.
- Preserve database-level invariants.
- Treat PostgreSQL as the source of truth for inventory availability.
- Redis must not become the primary availability authority.
- Consider transaction boundaries and race conditions.
- Preserve the reservation overlap protection.
- Preserve price snapshots on reservations.
- Preserve reservation event/audit behavior.

The critical invariant is:

> A cabin must not have more than one inventory-blocking reservation overlapping the same dates at the same time.

Blocking reservation statuses are:

```text
pending_payment
paid
confirmed
```

Reservation date ranges use `[)` semantics: check-out is exclusive.

When concurrent booking conflicts occur, the expected API behavior is an appropriate conflict response such as HTTP `409 BOOKING_CONFLICT`.

Destructive database changes require explicit confirmation unless the user explicitly requested them.

## 12. Testing and Verification

Testing should match the change.

For booking-related changes, consider:

- validation
- availability
- overlapping dates
- concurrent booking conflicts
- reservation state transitions
- payment behavior
- expiration behavior
- API response semantics

Before declaring work complete:

1. Run relevant tests.
2. Run relevant static/type checks when available.
3. Perform appropriate manual verification.
4. Inspect the final diff.
5. Update the change log.

## 13. Definition of Done

A task is complete when:

- The requested behavior is implemented.
- Relevant tests pass.
- Important edge cases are handled.
- No unrelated changes were introduced.
- Documentation is updated when the architecture/domain/API changed.
- `docs/logs/LOG-ID.md` is updated for meaningful work.
- Relevant Obsidian memory is updated when durable knowledge changed.
- The final diff is reviewed.
- Remaining limitations are explicitly reported.
