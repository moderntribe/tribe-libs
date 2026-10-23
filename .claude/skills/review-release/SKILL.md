---
name: review-release
description: Review a software release by cross-referencing a Jira release with a GitHub pull request to verify scope alignment. Use when the user asks to review a release, audit a release PR, check if a PR matches a Jira release, verify release scope, or compare Jira tickets against PR commits.
---

# Release Review

Verify that a GitHub pull request contains only work associated with the tickets in a Jira release. Requires two inputs from the user:

1. **Jira release URL** (e.g., `https://<org>.atlassian.net/projects/<KEY>/versions/<id>/tab/release-report-all-issues`)
2. **GitHub PR** (number or URL)

## Prerequisites

Stop if any of the prerequisites are missing and inform the user to install and activate.

1. Atlassian MCP server must be installed and active in order to retrieve Jira tickets
2. Github CLI must be installed and active in order to retrieve Github pull request

## Workflow

### Step 1: Extract Release Tickets

1. Parse the project key and version ID from the Jira URL.
2. Call `jira_get_project_versions` with the project key to resolve the version name from the ID.
3. Call `jira_search` with JQL `project = <KEY> AND fixVersion = '<version name>'` (limit 50) to get all tickets. Paginate if `total` exceeds 50.
4. For each ticket, call `jira_get_issue` to retrieve the summary, description, status, and assignee. Batch calls in parallel (4 at a time max).

### Step 2: Fetch PR Details

Run `gh pr view <number> --json title,body,state,baseRefName,headRefName,commits,files,mergedAt,mergedBy,labels,additions,deletions,changedFiles` in the repo workspace.

### Step 3: Map Commits to Tickets

For every commit in the PR, determine which release ticket it belongs to by checking (in order):

1. **Explicit ticket key in the commit message** (e.g., `LL25-40`).
2. **Branch name in merge-commit messages** (e.g., `feature/LL25-45/node-dependencies`).
3. **Commit content and context** -- infer from the author, files touched, and message when no key is present.

Classify each commit into one of:

- **Mapped** -- clearly associated with a release ticket.
- **Merge/maintenance** -- routine gitflow merges (develop into feature, production merge-backs). These are expected and low-risk.
- **Unassociated** -- no ticket reference and does not clearly fall under any release ticket's scope.

### Step 4: Identify Unassociated Work

Group unassociated commits by logical effort (e.g., by merged PR or author). For each group, note:

- Author(s)
- What changed (files and purpose)
- Risk level: **None** (docs only), **Low** (config/infra, no prod impact), **Medium** (removes tooling, changes process), **High** (production code changes without tracking)

### Step 5: Cross-Reference File Changes

Spot-check that file types make sense for the tickets they map to:

- Composer/PHP changes should map to BE tickets.
- JS/CSS/build-system changes should map to FE tickets.
- Infrastructure/deploy changes should map to infra/devops tickets (or be flagged if none exist).

### Step 6: Produce Report

Output a structured report with these sections:

#### Release Summary

| Field | Value |
|-------|-------|
| Jira Release | name + URL |
| PR | number + title + URL |
| State | merged/open |
| Total Tickets | count |
| Total Commits | count |

#### Commit-to-Ticket Mapping Table

| Ticket | Summary | Commit Count |
|--------|---------|:------------:|

#### Unassociated Work

For each group of unassociated commits:

- **Description** of the work
- **Author(s)**
- **Files affected**
- **Risk level** with brief justification

#### Verdict

| Category | Commits | % of Total |
|----------|:-------:|:----------:|
| Mapped to release tickets | n | x% |
| Merge/maintenance | n | x% |
| Unassociated (no ticket) | n | x% |

End with a plain-language summary:

- Whether the PR is cleanly scoped to the release.
- Which unassociated items (if any) are concerning.
- A recommendation: **Pass** (clean), **Pass with notes** (minor extras, low risk), or **Needs attention** (significant untracked work or high-risk changes).
