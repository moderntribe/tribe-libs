---
name: ticket-autopilot
description: Executes one or more Jira tickets end-to-end with minimal supervision. Use when the user asks to work tickets from implementation through PR and Jira updates, including branching, testing, review, and transition to Code Review.
---

# Ticket Autopilot

## Purpose

Run straightforward Jira tickets from start to code review with minimal user involvement, while enforcing explicit pause conditions for risky or ambiguous work.

## Inputs To Confirm

- Ticket IDs (one or many)
- Lane: `fast` or `guarded`
- PR strategy: `one-per-ticket` or `combined`
- Validation expectation: `required` or `best-effort`
- Jira transition target (default: `Code Review`)

If any input is missing, use safe defaults from project rules and continue.

## Required Workflow

Copy this checklist and execute in order:

Task Progress:
- [ ] 1. Retrieve ticket details
- [ ] 2. Determine branch + PR plan
- [ ] 3. Create working branch(es)
- [ ] 4. Plan implementation per ticket
- [ ] 5. Implement changes
- [ ] 6. Run validation (lint/tests/build as applicable)
- [ ] 7. Review implementation quality
- [ ] 8. Commit changes
- [ ] 9. Create PR(s)
- [ ] 10. Add Jira QA/UAT instructions
- [ ] 11. Transition Jira issue(s) to Code Review

## Step Details

### 1) Retrieve Ticket Details

- Pull summary, description, acceptance criteria, dependencies, assignee/status.
- Pause immediately if requirements are unclear or conflicting.

### 2) Determine Branch + PR Plan

- If `one-per-ticket`: create one branch and one PR per ticket.
- If `combined`: create one branch and one PR covering all tickets.
- Follow repository git conventions and existing commit style.
- Do not edit files in `main`; only in ticket branch(es).

### 3) Create Working Branch(es)

- Create the planned branch(es) immediately after Step 2.
- This is a hard gate: do not make any file edits until branch creation succeeds.

### 4) Plan Implementation

- Make a concise per-ticket implementation plan before editing.
- Reuse existing project patterns first.
- For external library/API setup or documentation questions, use Context7 MCP.

### 5-7) Implement, Validate, Review

- Implement the smallest complete change that satisfies acceptance criteria.
- Run relevant checks; fix issues introduced by the change.
- Perform a review pass for bugs, regressions, missing tests, and edge cases.

### 8-9) Commit + PR

- Create clear commit messages focused on intent.
- Open PR(s) with:
  - concise summary of changes
  - explicit test plan/checklist
  - any assumptions or follow-ups

### 10) Jira Testing Instructions

- Read and follow the `jira-testing-instructions` skill to add testing instructions to the ticket.
- The skill writes to the dedicated **Testing Instructions field** (`customfield_10040`) — do NOT use a comment as a substitute.
- The skill handles dev URL detection, required format (Environment / Preconditions / Steps / Expected Results / Regression Checks), and field update via `jira_update_issue`.

### 11) Jira Transition + PR Link

- Link PR(s) in a Jira comment when possible.
- Transition ticket status to the configured review state.

## Mandatory Pause Conditions

Pause and ask the user before continuing if any of the following appears:

- security/auth/permission implications
- data migration or destructive operations
- new dependency or service integration
- architecture-level decision or cross-subsystem refactor
- scope drift beyond ticket intent
- unresolved failures not clearly caused by the ticket work

## Final Report Format

When complete, provide:

- Ticket(s) completed and status
- Branch name(s) and PR URL(s)
- Validation performed and outcomes
- Jira updates applied (testing instructions + transition)
- Any residual risks or follow-up items
