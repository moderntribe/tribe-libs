# Remote Source Mode (V2 Proposal)

This document proposes a V2 enhancement to `tribe-skills` that allows the CLI to fetch skills from GitHub at runtime instead of requiring a local repository checkout.

Status: Draft only (not implemented)

## Goals

- Let engineers install/update skills from any machine without cloning the repo.
- Keep GitHub as the canonical source of truth.
- Preserve current local workflow for development and offline use.
- Make rollout low risk by adding remote behavior behind explicit source selection.

## Non-Goals (V2)

- No signed artifacts yet.
- No multi-registry support.
- No auto-updates in the background.
- No hosted UI/catalog app.

## High-Level Concept

Today, `tribe-skills` reads from local files:

- `skills.json`
- `skills/<skill-id>/...`

In V2, the CLI can optionally read from GitHub:

- Fetch `skills.json` from `raw.githubusercontent.com`
- Fetch each skill file listed for the selected skill
- Install into the same local destination (`~/.cursor/skills` by default)

## Source Modes

Introduce source selection:

- `local`: use repository files on disk only
- `remote`: use GitHub only
- `auto` (recommended default): prefer local when repo files are available, otherwise remote

Suggested environment variable:

- `TRIBE_SKILLS_SOURCE=auto|local|remote`

## Proposed Configuration

Environment variables:

- `TRIBE_SKILLS_HOME` (existing): install location (default `~/.cursor/skills`)
- `TRIBE_SKILLS_SOURCE`: source mode
- `TRIBE_SKILLS_REPO`: GitHub repo slug, e.g. `ModernTribe/tribe-skills`
- `TRIBE_SKILLS_REF`: branch or tag, e.g. `main` or `v1.2.0`
- `TRIBE_SKILLS_CACHE_DIR`: cache directory (optional, default `~/.cache/tribe-skills`)

Recommended defaults:

- `TRIBE_SKILLS_SOURCE=auto`
- `TRIBE_SKILLS_REPO=ModernTribe/tribe-skills`
- `TRIBE_SKILLS_REF=main`

## Remote URL Strategy

Use raw GitHub content for simplicity:

- Index:
  - `https://raw.githubusercontent.com/<org>/<repo>/<ref>/skills.json`
- Skill files:
  - `https://raw.githubusercontent.com/<org>/<repo>/<ref>/skills/<skill-id>/<filename>`

This avoids GitHub REST API pagination and reduces auth complexity for public repos.

## Command Behavior (Remote-Aware)

### `list [--label <label>] [--refresh]`

- Resolve source (`local` / `remote` / `auto`)
- Load index from selected source
- Apply label filter
- Print installed status using local state file

### `install <skill-id> [--refresh]`

- Load index
- Find skill by id
- Determine files to install (`SKILL.md`, `skill.yaml`, optional extras)
- Fetch each file from source
- Write files into `$TRIBE_SKILLS_HOME/<skill-id>/`
- Update installed state file

### `update [<skill-id>|--all] [--refresh]`

- Load latest index
- Compare installed versions vs available versions
- Re-install only changed skills
- Update state file versions

### `outdated [--refresh]`

- Load latest index
- Compare installed versions vs available versions
- Print current/outdated summary
- Exit non-zero when outdated skills exist (optional, useful for CI)

### `doctor`

- Existing checks remain
- If source includes remote, also check:
  - remote index reachability
  - cache health
  - clear error messages for DNS/network failures

## Caching Plan

Use light caching to improve reliability and speed.

- Cache `skills.json` with timestamp metadata
- Optional TTL (for example 5-15 minutes)
- `--refresh` bypasses cache and forces network fetch
- If remote fetch fails and cache exists, warn and use stale cache

Cache metadata example:

```json
{
  "url": "https://raw.githubusercontent.com/ModernTribe/tribe-skills/main/skills.json",
  "fetched_at": "2026-02-19T15:00:00Z",
  "ttl_seconds": 600
}
```

## Security Considerations

Minimum safeguards for V2:

- Validate skill id and filenames before writing locally.
- Reject path traversal attempts (`../`, absolute paths).
- Only install files explicitly declared by skill metadata/index.

Recommended V2.1 hardening:

- Add checksums for each file in `skills.json`.
- Verify downloaded file checksum before install.
- Prefer pinned tags (`TRIBE_SKILLS_REF=vX.Y.Z`) for reproducibility.

## Private Repository Support (Future-Friendly)

If repo becomes private:

- Support `GITHUB_TOKEN` for authenticated fetches.
- Optionally support `git clone` based fallback for authenticated environments.

V2 can begin public-first and keep auth support as an additive enhancement.

## Backward Compatibility

- Keep local mode behavior unchanged.
- No breaking changes to existing commands.
- Existing state file format should remain valid.

## Suggested Rollout

1. Add source abstraction layer in CLI (no behavior change yet).
2. Add remote loader behind `TRIBE_SKILLS_SOURCE=remote`.
3. Add cache + `--refresh`.
4. Validate with internal users on public repo.
5. Switch default to `auto` after confidence is high.

## Open Questions

- Should `outdated` return non-zero when anything is stale?
- What should default cache TTL be?
- Should we pin installs to tags by default in production docs?
- Do we want a lock file for reproducible team installs?

## Acceptance Criteria for V2

- A user can install a skill on a machine with no local repo checkout.
- `update --all` and `outdated` correctly use remote index data.
- CLI behavior is unchanged in `local` mode.
- Failures include actionable messages (network, missing skill, bad ref).
- README includes source mode examples and troubleshooting guidance.

