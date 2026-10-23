---
name: jira-ticket-standard
description: Creates and updates Jira tickets using the team's standard structure and language. Use when the user asks to create a Jira ticket, rewrite ticket descriptions, or standardize Goals/Outcomes/Assets formatting.
---

# Jira Ticket Standard

## Purpose
Create Jira issues that follow the team's standard structure and are implementation-ready.

## When to use
- User asks to create a Jira ticket
- User asks to rewrite or clean up a Jira ticket
- User provides Slack/Figma/URL notes and wants a proper Jira issue

## Required clarification behavior
If required details are missing, ask targeted follow-up questions before creating or updating the ticket.

**Required details:**
- Project key
- Issue type
- Summary/title
- Problem context
- Expected outcomes
- Relevant links/assets

**Rules:**
- Do not guess missing critical details.

## Workflow
1. Gather required details.
2. If anything critical is missing, ask concise follow-up questions.
3. Write description in this exact section order:
   - `### Goals`
   - `### Outcomes`
   - `### Assets`
4. Keep outcomes specific and testable.
5. Put links under Assets as bullets.
6. Create or update issue with Jira MCP tools.
7. Return a link to the issue and a concise summary.
8. The goal should clearly describe the bug to be fixed, feature to be implemented, or task to be completed.

## Title conventions
- Keep title short and action-oriented.
- Preserve team prefixes when supplied by user (examples: `FE:`, `BE:`).

## Description template
Use the template in [templates.md](templates.md).

## Quality checks
- Outcomes are observable and verifiable.
- Assets include environment URL(s) and source thread/design links when provided.
- No implementation assumptions beyond user-provided scope.
