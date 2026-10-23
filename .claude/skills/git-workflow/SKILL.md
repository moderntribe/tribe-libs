---
name: git-workflow
description: Git branch naming conventions and pull request standards. Use when creating branches, making commits, opening pull requests, or when the user asks about git workflow conventions.
---

# Git Workflow

## Branch Naming

Format:
- With ticket: `{type}/{TICKET-ID}/{short-description}`
- Without ticket: `{type}/{short-description}`

**Types:**
- `feature` - new functionality
- `bug` - bug fixes
- `task` - chores, refactoring, documentation

**Rules:**
- Use the ticket format when a ticket exists
- If there is no ticket, omit the ticket segment entirely (do not use placeholders like `NO-TICKET`)
- Lowercase short-description
- Hyphens between words (no underscores or spaces)
- Keep short-description to 2-4 words

**Examples:**
- `feature/PROJECT-123/add-menu-filtering`
- `bug/PROJECT-456/fix-date-parsing`
- `task/PROJECT-789/update-dependencies`
- `task/update-dependencies`

## Pull Requests

**Title format:** `[TICKET-ID] Short description`

Example: `[PROJECT-123] Add menu filtering by dietary restrictions`

**Before creating a PR**, check for a `.github/PULL_REQUEST_TEMPLATE.md` in the repo and use it to structure the description.

**Required sections (if no template exists):**
1. **What does this do/fix?** - Clear description of changes
2. **QA** - Link to Jira issue and include screenshots/video
3. **Checklist** - Complete all items before requesting review

**Rules:**
- Never deviate from defined title format
- Never deviate from PULL_REQUEST_TEMPLATE.md if one is found