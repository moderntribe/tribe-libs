---
name: promote-skill
description: Promotes a new skill or updates an existing skill in this tribe-skills repository from another local project. Use when the user wants to add a new skill here from project work, or when the user has modified an installed skill locally and wants to persist those changes back. Handles branch creation, packaging, validation, commit, pull request creation, and reinstall guidance.
---

# Promote Skill

Use this workflow when a user wants to contribute a skill from another local project to this repository. This covers two scenarios:

- **Promote (new)** — the skill does not yet exist in tribe-skills.
- **Update (existing)** — the skill already exists in tribe-skills and the user has made local changes they want to persist back.

Determine the scenario by checking whether `skills/<skill-id>/` already exists in this repository.

---

## Scenario A — Promote a New Skill

### Required Input

Collect these values before making changes:
- Source skill path on local disk (folder containing `SKILL.md`)
- Target `skill-id` (kebab-case, must match destination directory name)
- Skill display name
- Short description
- Labels list (at least one)
- Initial version (default `1.0.0`)
- Targets list — which tools this skill supports. Valid values: `cursor`, `claude`. Default: `cursor` only. Ask the user if they want the skill available for both.
- Optional ticket ID for branch/PR naming conventions

If metadata is missing from source files, derive it with the user before continuing.

### Workflow

1. Create branch in this repository
   - Follow repo conventions (for example: `feature/<ticket-or-scope>/add-<skill-id>`).
   - Confirm branch switch succeeded before file operations.

2. Add the skill folder under `skills/<skill-id>/`
   - Copy skill content from source path.
   - Ensure destination includes `SKILL.md`.
   - Remove irrelevant files (build artifacts, project-specific temp files, secrets).

3. Create or update `skills/<skill-id>/skill.yaml`
   - Must include: `id`, `name`, `description`, `version`, `labels`.
   - Ensure `id` exactly matches `<skill-id>`.
   - Keep description clear and specific.
   - Include `targets` list based on collected input (e.g. `[cursor]` or `[cursor, claude]`). Omitting `targets` defaults to `cursor`-only at install time, but explicitly stating it is preferred for clarity.

4. Run shared finalization steps (see **Shared Steps** below).

---

## Scenario B — Update an Existing Skill

### Required Input

Collect these values before making changes:
- Source skill path on local disk (the locally-modified copy containing `SKILL.md`)
- `skill-id` of the existing skill to update
- Optional ticket ID for branch/PR naming conventions

The agent should auto-detect the rest from the existing `skill.yaml` in tribe-skills.

### Determining What Changed

Before copying files, diff the local source against the current tribe-skills version to understand the scope of changes:
- Compare `SKILL.md` contents between source and `skills/<skill-id>/SKILL.md`.
- Compare any other files that exist in both locations.
- Summarize the meaningful differences for the user and confirm they want to proceed.

### Workflow

1. Create branch in this repository
   - Follow repo conventions (for example: `task/<ticket-or-scope>/update-<skill-id>`).
   - Confirm branch switch succeeded before file operations.

2. Sync changed files into `skills/<skill-id>/`
   - Copy updated files from source path, overwriting the existing versions.
   - Preserve any files in the destination that do not exist in the source (e.g. `skill.yaml` metadata the user didn't modify locally).
   - Remove irrelevant files (build artifacts, project-specific temp files, secrets).
   - Do NOT blindly replace the entire directory — be selective about what changed.

3. Bump version in `skills/<skill-id>/skill.yaml`
   - Read the current version.
   - Increment the PATCH version by default (e.g. `1.0.0` → `1.0.1`).
   - If the user indicates a larger change, bump MINOR or MAJOR instead.
   - Confirm the new version with the user.

4. Run shared finalization steps (see **Shared Steps** below).

---

## Shared Steps

Both scenarios finish with the same finalization:

1. Rebuild the index
   - Run `node scripts/build-index.js` to regenerate `skills.json`.
   - Confirm the skill entry is present and version is correct.

2. Validate
   - Run `node scripts/validate-index.js`.
   - Run `git status` and `git diff` to verify only intended files changed.

3. Run smoke install test (recommended)
   - Use isolated directories so the user's environment is untouched:
     - `tmp_dir="$(mktemp -d)"`
     - For each target in the skill's `targets` field (or `cursor` if omitted):
       - If target is `cursor`:
         - `TRIBE_SKILLS_HOME="$tmp_dir/cursor" TRIBE_SKILLS_STATE_FILE="$tmp_dir/state.json" bash cli/tribe-skills install <skill-id>`
         - `TRIBE_SKILLS_HOME="$tmp_dir/cursor" TRIBE_SKILLS_STATE_FILE="$tmp_dir/state.json" bash cli/tribe-skills doctor`
         - `TRIBE_SKILLS_HOME="$tmp_dir/cursor" TRIBE_SKILLS_STATE_FILE="$tmp_dir/state.json" bash cli/tribe-skills uninstall <skill-id>`
       - If target is `claude`:
         - `TRIBE_SKILLS_CLAUDE_HOME="$tmp_dir/claude" TRIBE_SKILLS_STATE_FILE="$tmp_dir/state.json" bash cli/tribe-skills install <skill-id> --target claude`
         - `TRIBE_SKILLS_CLAUDE_HOME="$tmp_dir/claude" TRIBE_SKILLS_STATE_FILE="$tmp_dir/state.json" bash cli/tribe-skills doctor --target claude`
         - `TRIBE_SKILLS_CLAUDE_HOME="$tmp_dir/claude" TRIBE_SKILLS_STATE_FILE="$tmp_dir/state.json" bash cli/tribe-skills uninstall <skill-id> --target claude`
     - `rm -rf "$tmp_dir"`
   - If install/doctor fails, fix before commit.

4. Commit
   - Stage changed skill files and updated `skills.json`.
   - For new skills: `feat(skills): add <skill-id>`
   - For updates: `fix(skills): update <skill-id> to <new-version>`

5. Create pull request
   - Push branch.
   - Open PR with summary, changelog of what changed, and test plan including build/validate/smoke-test commands.
   - Share PR URL with the user.

6. Final user notification
   - Confirm the workflow completed and include PR link.
   - Remind user to reinstall from tribe-skills after merge:
     - `tribe-skills uninstall <skill-id>`
     - `tribe-skills install <skill-id>`
   - If PR is not merged yet, note reinstall should happen after merge (or from branch checkout if they intentionally test pre-merge).

---

## Completion Criteria

Consider task complete only when:
- Skill exists in `skills/<skill-id>/` with valid `SKILL.md` and `skill.yaml`
- For updates: version has been bumped
- `skills.json` regenerated and valid
- Commit exists on remote branch
- PR is open and URL shared
- User receives uninstall/reinstall reminder
