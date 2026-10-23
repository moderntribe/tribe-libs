# svg-sanitize-update

## Ticket Information
- Status: In Progress (code done, audit clean; CHANGELOG kept under Unreleased (release script dates it); awaiting review/commit/tag)
- Objective: Resolve Dependabot/composer audit warnings for `enshrined/svg-sanitize` (<=0.22.0: GHSA-9rjx-3jch-6vjf, GHSA-m9xh-6747-9r6f, GHSA-v383-3rw5-q8rf) without blind package changes.

## Context
- Used only in `src/Media/` (`Media_Definer`, `Sanitize_Uploads`, `Svg_Meta_Store`, `Svg_Memory_Store`).
- 1.1.x returns `false` for more inputs; stores run under strict_types so `trim(false)` would TypeError.

## Implementation
- User approved updating only this package. `composer require enshrined/svg-sanitize:^1.1.1` (0.22.0 -> 1.1.1, no other lock changes). The old `^0.22` constraint blocked 1.x.
- Stores now `trim( (string) sanitize() )`; added `test_it_returns_empty_string_for_missing_svg`.
- CHANGELOG Unreleased entry added.

## Verification
- composer audit: 7 -> 4 advisories (svg-sanitize ones gone).
- Unit tests OK (43), phpcs clean on Media, phpstan no errors (needs `--debug`; sandbox blocks parallel workers).
- Not run: integration suite (needs WP DB). Not confirmed: release notes did not name the 3 GHSAs; audit output no longer lists them.

## Dev-only advisory updates (approved, done)
- `composer update guzzlehttp/guzzle squizlabs/php_codesniffer symfony/yaml --no-scripts --no-plugins`: guzzle 7.15.1->7.15.2, phpcs 3.13.5->3.13.6, symfony/yaml 7.4.14->7.4.21. Only those 3 changed.
- `composer audit --locked`: no advisories. Unit 43 OK, phpcs (whole repo) clean, phpstan OK.
- Gotcha: `--no-plugins` skips dealerdirect installer and the phpcs update wipes installed_paths; fixed by running `composer install --no-scripts` (plugins on). Do not use `--no-plugins` with phpcs updates.

- Version bump to 5.0.1: CHANGELOG stays under `## Unreleased`: AddTagToChangelogReleaseWorker only replaces that literal heading; `src/Media/composer.json` svg-sanitize pin ^0.22 -> ^1.1.1. Tag/mutual deps/branch alias left to monorepo release workers.

## Remaining Work
- Run integration tests with local WP; review SVG output for 1.1.x cleaning changes (uppercase USE/ID, use-threshold).
- Review and commit (nothing committed yet).

- Optional separate approval: dev-only patch updates for guzzlehttp/guzzle (>=7.15.2), squizlabs/php_codesniffer (>=3.13.6), symfony/yaml (>=7.4.18).
- Run integration tests with local WP; review SVG output for 1.1.x cleaning changes (uppercase USE/ID, use-threshold).
