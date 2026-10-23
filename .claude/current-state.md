# Current State

## Completed
- Ported Announcements from `moose-repo`: private admin CPT, ACF content/setup/display fields, placement/scheduling/page/archive rules, rendering pipeline, manual announcement block, and automatic renderer block.
- Registered the CPT, field group, and blocks; added above/below renderers to main and subsite headers.
- Adapted presentation to GU-Q tokens, typography, button markup/themes, responsive layout, and persistent dismissal behavior.
- Adapted newer Extended ACF calls to the installed 13.8 API without dependency changes.
- Refactored Announcements to ModernPress standards: injected rule dependencies, removed mutable pipeline state and unused APIs, consolidated front-page visibility, simplified rendering, and moved wrapper-attribute construction into the block controller.
- Added Network Admin → Settings → Announcements with per-site enablement, registered through the existing settings subscriber. `Announcements_Availability` gates both announcement blocks, the CPT, and its ACF field group; existing sites default to enabled until settings are saved.
- Passed PHPCS, PHPStan, JS lint, CSS lint, JSON validation, `git diff --check`, and the production block build.
- Existing subsite behavior: Admissions Mosaic block is hidden and selected editorial CPTs are not registered.

## Active
- [CIRS CPTs and taxonomies](tickets/cirs-cpts.md): code done and linted; needs runtime check on CIRS.

- [MOOSE-350 Faceted Directory review](tickets/MOOSE-350-faceted-review.md): findings reported, awaiting approval before refactor.

- [svg-sanitize update](tickets/svg-sanitize-update.md): svg-sanitize 1.1.1 + dev advisories fixed, composer audit clean; uncommitted, pending review and integration tests.

## Unresolved
- Browser/admin smoke test with a running WordPress database.

## Blockers
- Local WordPress database is not running, so runtime registration and visual checks could not execute.

## Next
- Start the local WordPress environment.
- Verify per-site toggles under Network Admin → Settings → Announcements and confirm disabled sites do not register announcement blocks, the CPT, or fields.
- Create announcements covering both placements, scheduling, visibility rules, themes, CTA styles, orientations, and dismissal persistence.
- Verify the main-site and subsite headers at mobile and desktop widths.
