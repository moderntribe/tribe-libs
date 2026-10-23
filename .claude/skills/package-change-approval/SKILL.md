---
name: package-change-approval
description: Require explicit user approval before installing unrequested packages or updating dependencies with any package manager. Use when considering Composer, npm, Yarn, pnpm, Bun, pip, Poetry, Cargo, Go modules, Bundler, Maven/Gradle, or similar dependency changes.
---

# Package Change Approval

## Purpose

This skill prevents unapproved dependency changes. Use it before installing, adding, removing, replacing, or updating packages through any package manager unless the user explicitly requested the exact package or version change.

Package managers include Composer, npm, Yarn, pnpm, Bun, pip, Poetry, Cargo, Go modules, RubyGems/Bundler, Maven/Gradle, and similar tools.

## Required Approval Checkpoint

Before changing dependencies, stop and ask the user for approval when either condition is true:

- A new package would be added and the user did not explicitly request that exact package.
- An existing dependency would be updated, replaced, or removed through any package manager.

Do not run install/update/add/remove commands until the user approves.

Examples of commands that require this checkpoint when not explicitly requested:

- `composer require`, `composer update`, `composer remove`
- `npm install`, `npm update`, `npm uninstall`
- `yarn add`, `yarn upgrade`, `yarn remove`
- `pnpm add`, `pnpm update`, `pnpm remove`
- `bun add`, `bun update`, `bun remove`
- `pip install`, `poetry add`, `poetry update`
- `cargo add`, `cargo update`
- `go get`, `go mod tidy` when it may change dependencies
- `bundle add`, `bundle update`, `gem install`
- `mvn versions:*`, Gradle dependency updates

## Approval Request Format

When asking for approval, include:

- Package: exact package name and requested version/range if known.
- Source: registry, repository, or other source where the package comes from.
- Purpose: what the package does.
- Why needed: why the current task needs this package.
- Risk check: how suspicious it appears, including maintenance, popularity, license, known security concerns, and whether the name looks typo-prone or package-squatting-prone.
- Alternatives: built-in APIs, existing project dependencies, or other packages considered.
- Impact of not adding/updating: what functionality, quality, or speed would be affected.

Use a concise format with a short paragraph first, then grouped details:

```markdown
I need approval before changing dependencies. I want to add/update `name@version` because [one-sentence reason]. This is [low/medium/high] suspicion based on [brief reason].

Package details:
- Package: `name@version`
- Source: registry or URL
- Purpose: ...

Assessment:
- Why needed: ...
- Risk check: ...
- Alternatives: ...
- Impact if skipped: ...

May I proceed?
```

## Explicit User Requests

If the user explicitly asks to install or update a specific package, proceed with that package only. Do not introduce related packages, peer dependency fixes, lockfile updates beyond what the package manager requires, or broader dependency updates without a separate approval checkpoint.

If a package manager command will change more dependencies than expected, stop and ask for approval before continuing.
