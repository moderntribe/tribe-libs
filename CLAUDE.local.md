## Persistent Project Memory

@.claude/current-state.md

Maintain persistent project memory across sessions using two levels:

1. `.claude/current-state.md` — concise project overview and active ticket index.
2. `.claude/tickets/` — detailed, independent memory files for individual tickets.

### Current Project State

Maintain `.claude/current-state.md` as a lightweight overview.

Track:
- Active tickets with links to their memory files.
- Recently completed tasks.
- Unresolved tasks.
- Project-level blockers.
- Important architectural decisions.
- Next recommended actions.

Rules:
- Read the current state before starting project work.
- Keep the file concise and avoid storing detailed ticket histories.
- Reference individual ticket files rather than duplicating their contents.
- Update the state when ticket statuses or project priorities change.
- Remove outdated information while preserving important decisions.
- Do not modify the file when nothing meaningful has changed.

### Ticket Memory

Store ticket-specific information in `.claude/tickets/`.

Each ticket MUST have its own Markdown file when work is substantial or spans multiple sessions.

Naming conventions:
- Use the original ticket identifier when available, e.g. `PROJ-123.md`.
- Otherwise use a concise, descriptive filename, e.g. `wordpress-cache-issue.md`.
- Reuse existing ticket files rather than creating duplicates.
- Do not rename ticket files unnecessarily.

Each ticket file should contain:

#### Ticket Information
- Ticket ID or descriptive name.
- Status: Not Started, In Progress, Blocked, Completed.
- Objective and expected outcome.
- Scope and requirements.

#### Context
- Relevant project architecture.
- Related files, classes, functions, and components.
- Environment-specific considerations.
- Relevant constraints and dependencies.

#### Investigation
- Reported symptoms and observations.
- Root-cause hypotheses.
- Confirmed findings with supporting evidence.
- Investigations already performed.
- Approaches attempted and their results.

#### Implementation
- Changes made.
- Important implementation details.
- Architectural and technical decisions.
- Reasons for choosing or rejecting approaches.

#### Verification
- Tests and checks performed.
- Actual verification results.
- Known limitations.
- Outstanding testing requirements.

#### Remaining Work
- Unresolved problems.
- Blockers.
- Next recommended actions.

### Ticket Lifecycle

When starting work:
- Read `.claude/current-state.md`.
- Identify the relevant ticket from the user's request and available project context.
- Check whether a corresponding ticket memory file exists.
- Read only the relevant ticket file, not the entire tickets directory.
- Resume from previously recorded findings without repeating completed investigations.
- If the task is substantial and no ticket file exists, create one.
- Do not create ticket files for trivial questions or one-off operations unless explicitly requested.

During work:
- Use the ticket file as persistent context for the active task.
- Preserve important findings, decisions, and implementation details.
- Distinguish confirmed facts from hypotheses.
- Avoid repeating investigations already documented unless new evidence requires it.
- Do not write speculative conclusions as confirmed findings.
- Do not write raw conversation transcripts or unnecessary tool outputs.

After meaningful work:
- Update the relevant ticket file, preferably once at the end of the task.
- Record completed changes and confirmed investigation results.
- Record what was tested and what remains unverified.
- Record unresolved problems and next actions.
- Update `.claude/current-state.md` if the ticket status or project state changed.
- Do not mark a ticket completed merely because code was written or a fix was proposed.
- Mark it completed only when the requested work is finished, noting any verification limitations.
- Preserve completed ticket files for future reference.

### Memory Quality and Efficiency

- Keep ticket memory detailed enough to resume work in a new session without repeating investigation.
- Prefer structured summaries over verbose chronological logs.
- Preserve the reasoning behind important technical decisions.
- Include relevant file paths and code references when useful.
- Never invent missing ticket details.
- Never store secrets, credentials, tokens, or sensitive data.
- Do not access external ticket systems without explicit authorization.
- Do not automatically load all ticket files into context.
- Do not duplicate detailed information between ticket files and current state.
- Do not modify unrelated ticket files.
- Do not create additional memory systems or documentation files unless explicitly requested.

## Lando Container Access

- Lando containers belonging to the currently selected project may be inspected for debugging purposes.
- Use Lando only for read-only diagnostics unless explicitly authorized otherwise.
- Permitted diagnostic activities include viewing logs, checking runtime versions, inspecting configuration, and executing read-only queries.
- Ask for approval before running any command inside a container.
- Do not install packages, update dependencies, write to databases, change configuration, or restart, rebuild, or destroy containers without explicit authorization.
- Do not inspect containers belonging to other projects.
- Never treat Docker access as permission to inspect unrelated host files.
- If a diagnostic command requires broader access, explain why and request approval.
