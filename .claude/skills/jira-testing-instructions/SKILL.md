---
name: jira-testing-instructions
description: Adds precise QA and UAT testing instructions to Jira tickets using implementation-aware context. Use when the user specifically asks to add testing instructions while working a Jira ticket.
---

# Jira Testing Instructions

## Purpose

Add implementation-aligned testing instructions to Jira tickets.

## Strict trigger

Use this skill only when:

- A Jira ticket is in active work context, and
- The user explicitly asks to add testing instructions.

## Required URL confirmation (first step)

Before drafting testing instructions, identify the project dev URL from the working repository when possible, then ask:
"Do you have specific test URL(s) you want used in the instructions? If not, I will use: [detected dev URL]."

## Dev URL detection rules

Determine the default dev URL in this order:

1. Check `config/.vip.*.develop.yml` in the current repository.
2. In `data_sync.domain_map`, select the mapped domain that represents the primary site root (typically the mapping for `www.<site>.edu` or the first root domain entry).
3. Normalize to an https URL, for example `https://dev.example.com`.
4. If no reliable URL can be detected, ask the user for the preferred test URL before continuing.

Do not assume specific domains unless the repository config indicates them.

## Evidence sources (in priority order)

Build testing instructions from actual implementation context, not only ticket text:

1. Current chat context (what was implemented/fixed)
2. Any AI plan created in this conversation
3. Git context (current branch, branch name patterns tied to ticket key)
4. GitHub PR context (changed files, PR description, acceptance notes)
5. Jira ticket description/outcomes (fallback/supporting context)

## Branch/PR fallback requirement

If a concrete code solution cannot be identified from current context, ask the user for one of:

- Branch name containing the ticket key, or
- GitHub PR link

Do not produce final testing instructions until implementation evidence is available.

## Workflow assumptions

- Always place the testing instructions into the field labelled "Testing Instructions". I've seen this field with the `customfield_10040` key, but it could be different. You may have to search for it.
- If the "Testing Instructions" field doesn't exist, append the testing instructions to the ticket description using the `### Testing Instructions` heading as a prefix. You do not need to include this heading if the "Testing Instructions" field exists.
- Always use Jira wiki markup for the testing instructions when writing through the Atlassian MCP. Links should be clickable when generated.
- Do not use Markdown backticks for code formatting in Jira fields; Jira often displays them literally.
- Use Jira wiki inline code formatting with double curly braces, for example `{{menu-open}}`, for identifiers that do not contain Jira issue keys.
- Use `{noformat}` blocks for branch names, commands, paths, or identifiers that contain Jira issue keys. This prevents Jira from auto-linking the ticket ID inside the value.
- Do not write raw branch names like `task/COMIC25-49/update-dependencies` outside a `{noformat}` block unless the ticket auto-link is intentionally desired.

## Workflow

1. Detect default dev URL for the current project.
2. Confirm preferred test URLs with the user; use the detected default when none are provided.
3. Read Jira ticket context.
4. Read implementation context from chat/plan/branch/PR.
5. Map each implemented change to one or more validation steps.
6. Include:
    - Environment URL(s)
    - Preconditions
    - Numbered validation steps
    - Expected result per step
    - Regression checks around touched areas
7. Keep steps specific to the implemented solution.

## Required format

*Deploy Branch:*
{noformat}
[Current git branch]
{noformat}

*Environment:*

* [URL(s)]

*Summary:* [A quick summary of the changes made as related to the ticket description]

*Preconditions:*

* [Required data/settings/login/content]

h4. Steps

# [Action]
# [Action]
# [Action]

h4. Expected Results

* [Result tied to Step 1]
* [Result tied to Step 2]
* [Result tied to Step 3]

h4. Regression Checks

* [Nearby behavior 1]
* [Nearby behavior 2]

## Quality checks

- Every step validates code that was actually changed.
- Expected results are observable and binary (pass/fail).
- Instructions are executable by QA/UAT without hidden assumptions.
- URLs/environment are explicit.
