# Development Logs

This directory contains canonical project change logs.

## Naming

Use sequential IDs when practical:

```text
LOG-001.md
LOG-002.md
LOG-003.md
```

## Required Content

A meaningful log should normally contain:

- Log ID
- Date
- Change type
- Status
- Summary
- Changes made
- Files changed
- Database changes, if any
- API changes, if any
- Important technical decisions
- Tests and verification performed
- Remaining issues or follow-up work

## Obsidian

A corresponding development note may be created in the Obsidian vault under:

```text
04-Development-Logs/
```

The repository `docs/logs/LOG-ID.md` remains the canonical project history.

## Security

Never store:

- passwords
- API keys
- access tokens
- database credentials
- payment secrets
- private customer data
- other sensitive information
