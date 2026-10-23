# MOOSE-350 – Faceted Directory review (branch feature/MOOSE-423/faceted-directory-custom-refactored vs main)

## Ticket Information
- Status: In Progress (review done, awaiting user approval of findings; no code changed)
- Objective: correct review + ModernPress-standard refactor of Faceted Directory without changing end behaviour (spec: MOOSE-350.md).

## Context
- PHP: `plugins/core/src/Facets/*`, `Components/Blocks/{Filter_Bar,Directory_Grid,Faceted_Directory}_Controller.php`, `Settings/{Facets,Tribe}_Settings.php`, `Abstract_Block_Controller` (context/preview additions).
- Theme: `blocks/tribe/{faceted-directory,filter-bar,directory-grid}`, `components/{filter-bar,directory-grid}`.
- Stray untracked files (Facet_Index_Hooks, FacetQueryTest, FacetsWpUnitTest) were not part of the branch; user deleted them. Branch has NO Facets tests. Findings based on them were dropped.

## Investigation (confirmed)
- phpcs on Facets/Blocks/Settings: clean. phpstan/codeception NOT run (lando not on sandbox PATH; local PHP 8.2 cannot parse typed consts).
- Live REST: `facet_search[a]=b` / `post_types[1][x]=y` -> "Array to string conversion" warnings (Facet_Registry:160, Directory_Query:175) on a public endpoint. WPUnit tests already expect this fixed.
- `components/filter-bar/top.php` unreferenced; PHP `displayLabel` key unused.
- `rebuild()` returns before setting BUILT when no facets -> admin stuck on "Not built".
- Transient keys derive from unvalidated user term slugs -> unbounded option rows.

## Decisions (user)
- Scope items left as is, to be revisited later: global post-card placeholder (Post_Data/cards), top-level Tribe menu + Login move (top-level confirmed wanted), screen-reader-only rename, querypagination rewrite, Abstract_Block_Controller preview context.

## Implementation (phases 1-2 done, uncommitted)
- Index split: Facet_Index_Table (schema/state), Facet_Indexer (write, queue flushed on shutdown), Facet_Index (read, no result transients). Facet_Index_Admin service; Facets_Subscriber only registers hooks. \wpdb injected via Facets_Definer.
- Fixes: nested-array warnings, rebuild with no facets marks built, writes limited to indexed types/taxonomies and skipped while unbuilt, facet params limited to facets of the directory post types, REST args schema.
- User decision: NO tests for Faceted ("none can confirm anything"); tests were written then deleted.
- Verified: phpcs clean; live REST valid request OK, nested search -> 400. phpstan result not seen (terminal hung); user to run `lando composer phpstan`.

- Phase 3 done (uncommitted): Facet_Terms (memoized options, nest/ancestors/availability, one-query descendant expansion); Facet_Renderer::get_control_id public and reused by Filter_Bar_Controller; filter_public_post_types(mixed); context consts on Faceted_Directory_Controller; Filter_Bar/Directory_Grid controllers use constructor injection (no container lookups); Facets_Settings reuses public_post_types(). Live: /facets/ renders 4 facets, labels resolve, category filter returns results, unknown facet param ignored. phpcs clean, phpstan not run.

- Phase 4 done (uncommitted): build_tax_clauses private instance; public_post_types/filter_public_post_types now instance methods (Facet_Registry injected into Directory_Grid_Controller and Facets_Settings); removed get_by_slug, `type` alias, PHP displayLabel key, Faceted_Directory post_types, components/filter-bar/top.php; typed consts. Left: Facet_Types statics (pure constants helper). Live /facets/ OK; settings screen + editor not checked (pane not logged in); phpstan not run.

- Resumable rebuild (host 30s limit), uncommitted: Facet_Indexer split into Facet_Index_Writer (replace/delete rows, batched term cache + multi-row INSERT), Facet_Index_Sync (queue/flush/term hooks), Facet_Index_Rebuilder (start/process/run, time budget, cursor in Table progress option, cron resume + 60s safety net). Table gained truncate/is_writable/get_progress/set_progress; Registry gained get_facet_post_types/uses_taxonomy. Admin shows progress; INDEXED_PARAM notice removed. docs/faceted-directory.md updated. UNTESTED at runtime: rebuild needs admin login (button) - user to run Rebuild Index on a larger dataset and phpstan.

## Remaining Work
- JS/CSS pass dropped by user (not doing it). PHP refactor phases 1-4 complete; remaining: user to run phpstan, check settings screen + editor, then commit.
- Await user decision on findings list (see chat review), then implement in phases.
