---
name: port-to-modernpress
description: Backports code improvements, features, or bug fixes discovered during a client project back to the ModernPress WordPress starter framework. Use when the user wants to contribute improvements from client work back to the ModernPress repo, including branching, applying changes, committing, and creating a pull request.
---

# Port to ModernPress

## Purpose

Backport improvements, bug fixes, or new features discovered during client project work back to the ModernPress WordPress starter framework (`https://github.com/moderntribe/moose`).

## When to Use

Use this skill when the user wants to:
- Port a new feature or utility built for a client back to the ModernPress starter
- Contribute a bug fix or enhancement from a client fork back to the base framework
- Sync an improvement discovered during client work to the starter so future projects benefit

---

## Inputs to Collect

Before starting, confirm with the user:

1. **ModernPress repo path** — ask the user for the local path to their ModernPress repo, or attempt to discover it (see Discovery below). Do not assume a default path.
2. **What to port** — description of the feature/fix/improvement; may be provided as code snippets, file paths in the client repo, or a plain-language description
3. **Source path** (optional) — local path to the client project repo so files can be read directly (e.g. `~/code/tribe/client-acme`)
4. **Branch description** — short slug for the new branch (e.g. `add-cache-utility`, `fix-block-spacing`)
5. **Change type** — `feature`, `task`, or `bug` (default: `task`)
6. **PR context** — brief explanation of why this change is valuable for the starter framework

If any required input is missing, ask before making any changes.

### Discovering the ModernPress Repo Path

If the user does not provide a path, attempt to locate the repo automatically before asking:

1. Check common locations:
   - `~/code/tribe/moose`
   - `~/Sites/moose`
   - `~/Projects/moose`
2. For each candidate, verify it is the correct repo:
   ```bash
   git -C {path} remote get-url origin
   ```
   Confirm the remote URL contains `moderntribe/moose`.
3. If exactly one match is found, confirm it with the user before proceeding.
4. If no match is found or multiple matches exist, ask the user to provide the path explicitly.

---

## Workflow

### 1. Sync ModernPress to Latest Main

Navigate to the ModernPress repository and ensure you are on a clean, up-to-date copy of `main`:

```bash
git -C {moose-path} status
git -C {moose-path} checkout main
git -C {moose-path} fetch origin
git -C {moose-path} pull origin main
```

**Pause if:**
- `git status` shows uncommitted changes — notify the user and do not proceed
- `git pull` fails or shows conflicts — report the error and stop

### 2. Create a New Branch

Follow the `git-workflow` skill conventions. Since backports are not tied to a Jira ticket, omit the ticket segment:

- Format: `{type}/{short-description}`
- Examples: `task/add-cache-utility`, `feature/responsive-nav-helper`, `bug/fix-block-spacing`

```bash
git -C {moose-path} checkout -b {type}/{short-description}
```

Confirm the branch was created before touching any files.

### 3. Apply the Changes

Apply the code described by the user. Depending on what was provided:

- **File paths in client repo** — read those files and copy relevant code to the equivalent ModernPress paths
- **Code snippets** — write them into the appropriate ModernPress files
- **Plain description** — implement the change following existing ModernPress patterns

**Rules:**
- Make minimal, focused changes — only port what was described
- Do not refactor or improve unrelated code
- Replace any client-specific values (project slugs, domain names, client theme names) with sensible ModernPress defaults or placeholders
- Confirm no secrets, local config, or client-confidential logic is included

### 4. Add Changelog Entry

Add a new entry to `CHANGELOG.md` in the ModernPress repo. The changelog follows [Keep a Changelog](https://keepachangelog.com/en/1.0.0/) with a date-versioned format specific to this project.

**Format:**

```
## [YYYY.MM.DD]
- {Category}: {Description of change}
```

**Category prefixes:** `Added`, `Updated`, `Fixed`, `Removed`, `Changed`, `Security`, `Deprecated`

**Rules:**
- Insert the new section immediately after the `# Changelog` header block (before the first existing `## [...]` entry)
- Use today's date in `YYYY.MM.DD` format for the section header
- If a section for today's date already exists, append the new bullet to that section rather than creating a duplicate header
- Choose the category that best matches the change type:
  - `Added` for new features or files
  - `Updated` for modifications to existing behavior
  - `Fixed` for bug fixes
- Keep the description concise and written from the perspective of someone using ModernPress as a starter — focus on what changed and why it matters, not implementation details

**Example:**
```
## [2026.03.15]
- Added: Reusable cache utility helper for reducing redundant remote requests
```

### 5. Review Before Committing

Before creating the commit:
- Run `git diff` and review all changes
- Verify there are no client-specific values left
- Check that namespacing, theme slugs, and file paths are appropriate for the ModernPress starter context
- If the change touches JavaScript, run the JS build to verify it compiles:
  ```bash
  cd {moose-path} && npm run build
  ```
- If the change touches PHP dependencies, run:
  ```bash
  cd {moose-path} && lando composer install
  ```

If issues are found, fix them before committing.

### 6. Commit

Write a clear, intent-focused commit message:

- New feature: `feat: <description>`
- Task / chore: `task: <description>`
- Bug fix: `fix: <description>`

Example: `feat: add reusable cache utility helper`

Stage only the files involved in the backport.

### 7. Push and Create Pull Request

```bash
git -C {moose-path} push -u origin {branch-name}
```

Create a PR against `main` in the ModernPress repository. Always check for `.github/PULL_REQUEST_TEMPLATE.md` and use it to structure the description. Assign the PR to the current user (`--assignee @me`).

**PR title format:** Short description of the backport (no ticket prefix needed since this is a starter improvement, not a client ticket)

**Fill out the PR template:**
- **What does this do/fix?** — Describe what was ported and why it benefits the starter. Mention the client project context if it helps reviewers understand the motivation (without exposing confidential details).
- **QA** — Describe how reviewers can verify the change. Link to relevant ModernPress issues if any exist.
- **Checklist** — Complete all applicable items from the template.

### 8. Confirm Completion

Report back to the user with:
- The branch name created in ModernPress
- The PR URL
- A summary of what was changed

---

## Completion Criteria

Consider the task complete only when:
- [ ] ModernPress repo path is confirmed
- [ ] ModernPress `main` was freshly pulled before branching
- [ ] Changes are on a new branch following naming conventions
- [ ] Code is free of client-specific values, secrets, and local config
- [ ] Build steps pass (if applicable)
- [ ] `CHANGELOG.md` updated with a new dated entry
- [ ] Commit message is clear and intent-focused
- [ ] PR is open against `main` with a filled-out description
- [ ] PR URL is shared with the user

---

## Notes

- Always branch off a fresh `git pull` of `main` — never off a stale local state or a client branch
- If the ported change depends on client-specific infrastructure that does not exist in ModernPress (e.g. a third-party plugin only on the client project), note this gap clearly in the PR description so reviewers can decide how to generalize it
- When in doubt about whether a pattern fits ModernPress, look at adjacent files in the repo to match existing conventions before adding new ones
