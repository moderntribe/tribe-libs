---
name: update-frontend-dependencies
description: Review and methodically upgrade frontend dependencies managed in package.json across projects. Use when the user wants to audit outdated or deprecated packages, remove unused dependencies, update Node, assess breaking changes, replace abandoned libraries, or execute staged dependency upgrades with a manual approval gate between planning and implementation.
---

# Update Frontend Dependencies

## Default Stance

- Treat this as a two-phase workflow.
- Stop after Phase 1 and wait for explicit user approval before making changes.
- Default Phase 2 order: `dependencies` first, then `devDependencies`.
- Within each phase, handle low-risk patch and minor updates before major updates, breaking-change migrations, and replacement work.
- Update one direct dependency at a time unless Phase 1 identifies a tightly-coupled set that must move together.
- Never remove a dependency only because it is not imported in app code; also check scripts, configs, styles, tests, build tooling, and CI.
- Prefer the safest path when ambiguous. If two approaches seem viable, choose the lower-risk option or stop and ask.

## Browser Review Tooling

Browser automation is recommended, not assumed.

Use the best available option in this order:

1. If an `agent-browser` skill is available, use it for local browser testing.
2. If another browser automation skill or tool is available, such as `playwright-cli` or Cursor browser tooling, use that instead.
3. If no browser automation is available, continue with build and static validation, then give the user a concise manual QA checklist for the relevant pages or flows.

Do not block a dependency update only because `agent-browser` is missing. In Phase 1, call out the browser review approach and recommend installing a browser automation skill when automated browser QA is important for the project.

## Project Detection

1. Read `package.json`.
2. Detect the package manager from `packageManager` and lockfiles.
3. Collect Node version signals from `engines.node`, `.nvmrc`, `.node-version`, `.tool-versions`, `volta`, Docker files, CI workflows, and docs.
4. Identify the real frontend build script. Prefer `npm run dist` when it exists and is the normal asset build; otherwise use the project's standard asset build command.

## Phase 1: Review And Upgrade Plan

### 1. Inventory

- List direct `dependencies` and `devDependencies`.
- Group tightly-coupled packages such as framework ecosystems, bundler plugins, Babel stacks, Sass/PostCSS chains, lint stacks, Storybook add-ons, Grunt/Gulp plugins, or CMS-specific tooling.

### 2. Find Removal Candidates

- Search for package usage in source imports, scripts, config files, tests, styles, and build tooling.
- Mark a package removable only when there is no meaningful reference and no implicit tooling role.
- If uncertain, keep it as `verify before removal` instead of deleting it.

### 3. Check Node

- Decide whether the current Node version is outdated, unsupported, or incompatible with planned upgrades.
- Prefer a supported LTS unless the project has a documented constraint.
- Call out every file and workflow that must move with the Node upgrade.

### 4. Check Outdated, Deprecated, And Abandoned Packages

- Use the active package manager's native commands to identify outdated direct dependencies.
- Inspect registry metadata for deprecation notices, latest version, engine requirements, and peer dependency expectations.
- For dependencies that appear unmaintained, review release recency, repository activity, and official guidance.
- If a package is abandoned or deprecated without a safe upgrade path, propose replacement options and expected migration scope.

### 5. Review Docs Before Recommending Updates

- Use Context7 first for current package docs and migration guidance.
- Review official changelogs or migration guides for every major upgrade and any package that affects build, routing, state, forms, styling, or linting.
- Record breaking changes, required code or config edits, dropped Node support, and peer dependency constraints.
- Separate low-risk patch or minor updates from major or migration-heavy work.

### 6. Produce The Approval Package

Use the Phase 1 template in [templates.md](templates.md). Include:

- removable packages and confidence level
- recommended Node target and why
- grouped upgrade sequence, with patch and minor updates before major-risk work
- packages with major-version risk
- abandoned or deprecated packages and alternatives
- expected validation steps
- blockers, open questions, and recommended order of operations

## Approval Gate

Stop here. Do not edit `package.json`, lockfiles, build config, or source code until the user explicitly approves Phase 2.

## Phase 2: Methodical Upgrades

Work through the approved plan in order.

### 1. Prepare

- If Node is changing, update the agreed version files first and reinstall with the project package manager.
- Keep the lockfile in sync with the active package manager.
- If Phase 1 identified a coupled package set that must move together, treat that set as one update unit.

### 2. Upgrade Runtime Dependencies

Start with approved patch and minor updates. Move to approved major updates and migration-heavy work only after the lower-risk runtime updates are stable.

For each approved runtime dependency or approved group:

1. Update to the target version.
2. Review the codebase for affected usage and apply only the changes required for that package.
3. Reinstall if needed and resolve peer dependency issues deliberately; do not paper over them with broad compatibility flags unless the user approves.
4. Run the frontend build. Prefer `npm run dist`; otherwise run the normal asset build script.
5. Use the best available browser review tooling. Review relevant local pages for visual issues and console errors, or document the manual QA checklist if automated browser testing is unavailable.
6. If the change is unstable or reveals broader modernization work, stop and surface options before continuing.
7. Record what changed and residual risk, then move to the next dependency.

### 3. Upgrade Dev Dependencies

Repeat the same one-at-a-time process for `devDependencies` after runtime dependencies are stable.

- Give extra attention to bundlers, task runners, loaders, transpilers, linters, test runners, Sass, PostCSS, and legacy build plugins.
- Handle low-risk patch and minor updates before major toolchain jumps or packages with breaking changes.
- When a devDependency upgrade implies build-system replacement, stop and propose a modernization track instead of improvising a full toolchain rewrite.

### 4. Final Validation

- Remove all node dependencies (rm -rf node_modules) and the package-json.lock file.
- Run the full frontend build again.
- Use the best available browser review tooling to review the most relevant pages or flows in the local environment.
- Check for console errors, broken interactions, styling regressions, and asset issues.
- Summarize any items not fully verified.

## Legacy And High-Risk Projects

If the project is far behind:

- Do not blindly upgrade everything.
- Split the work into tracks:
  - foundational runtime and platform changes
  - build-tool modernization
  - library replacement migrations
  - low-risk version bumps
- Recommend a safer sequence, expected blast radius, and where manual checkpoints are required.
- Get user approval before replacing a build system, swapping a core library, or changing the package manager or lockfile format.

## Reporting

Use the final report template in [templates.md](templates.md).

Always include:

- exact packages updated or removed
- Node change, if any
- code or config changes required
- validation performed
- highest regression risks and what still needs manual QA
