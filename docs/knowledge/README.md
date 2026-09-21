# Project Knowledge

This directory contains durable project knowledge that belongs in the Git repository.

## Purpose

Use this area for information that should remain useful after the current task is finished:

- architecture knowledge
- domain rules
- architectural decisions
- conventions
- recurring troubleshooting knowledge
- integration knowledge

Do not turn this directory into a copy of the entire codebase or a transcript of development conversations.

## Relationship to Obsidian

Obsidian is the project's separate knowledge workspace.

The Git repository remains the canonical source of truth.

When durable knowledge changes:

1. Update the appropriate repository knowledge document.
2. Update corresponding Obsidian memory when useful.
3. Avoid maintaining conflicting versions.

## Suggested Areas

```text
architecture/
decisions/
domain/
conventions/
troubleshooting/
integrations/
```

Create a subdirectory/file only when there is actual durable knowledge to store.
