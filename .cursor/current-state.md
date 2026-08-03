# tribe-libs — project memory

Updated: 2026-07-23

## Goal
Ship **5.0** PHP 8.1+ ready tribe-libs with real 4→5 back compat.

## Completed
- PHP `^8.1`, php-di `^7.0`, Spatie removed → first-party Field_Models DTO (+ BC names/aliases).
- square1 interdeps: `self.version`.
- Implicit nullable fixes; WP_Downloader `#[AsCommand]`.
- Tests: Codeception 5 suite conflict fixed; PHP-DI `useAttributes`; theme `twentytwentyfour`; wp-browser `^4.7`; Logger PSR-3; Container PHP-DI 7 ctor; collection casting; YouTube oEmbed skip when offline.
- Extra unit coverage for 5.0 surfaces: Field Model DTO/ValueCaster/FieldValidator, Spatie aliases, Container wrap/flush/makeFresh, Logger Stringable PSR-3, WP_Downloader `#[AsCommand]`, Swatch helpers.
- **Unit OK (42). Integration OK (96, 1 skipped offline oEmbed).**
- Lando: created `tribe_libs_test` + granted `wordpress` user.

## Active
- None.

## Unresolved
- Create `5.x` branches in sub-repos (workflow_dispatch `sub-repo-branch-create`) before first 5.0 tag split.
- Optional: commit vs ignore `composer.lock` policy for the monorepo.
- Monorepo release must run mutual-deps worker so `self.version` becomes real versions on tag.

## Docs
- Root README: 5.x Field Models + Mutable Container quick examples.
- Field_Models README: custom models, collections, castValue BC, Spatie aliases.
- Container README: notes PHP-DI 7.

## Next
1. Run `Create Sub-Repo Branches` with input `5.x` before releasing.
2. Tag/release 5.0.0 when ready.
