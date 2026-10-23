# Optimization & Reliability Plan

How to make the `modernpress-coding-standards` skill more reliable, cheaper to attach, and
easier for AI agents to follow. This is a planning doc, not part of the attached skill.

## Current shape

- `SKILL.md` — lean, attachable entrypoint (the only file usually loaded into a prompt).
- `governance-template.md` — deeper reference (scope, limits, checklist, acceptance).
- `examples.md` — paired good/bad snippets.
- `optimization-plan.md` — this file.

Rationale: keep the always-loaded surface small; let agents pull depth on demand. This is the
main token lever.

## Token reduction opportunities

- Keep `SKILL.md` as the single attached file; reference companions by name so they load only
  when needed. (Done.)
- Prefer one canonical statement of each rule in `SKILL.md`; put elaboration/examples in the
  companions instead of repeating.
- Trim the frontmatter `description` to the trigger conditions only. (Done.)
- Use terse rule lists over prose paragraphs; avoid restating WordPress basics agents know.
- Avoid duplicating full code blocks between `SKILL.md` and `examples.md` — the ternary
  example is intentionally the one shared anchor; keep other long snippets in `examples.md`.

## Duplicate guidance removed / consolidated

- Escaping, "thin templates," and "delegate hooks to services" appeared in several sections;
  now stated once in the relevant section and cross-referenced.
- The `Primary_Term` reuse note is stated once (Architecture) instead of repeated.
- Review expectations consolidated into one short list in `SKILL.md` + the full checklist in
  `governance-template.md`, rather than scattered "remember to…" lines.

## Missing guidance added

- Pragmatic OOP + explicit "when to add abstraction" triggers.
- The getters/setters nuance (intent-revealing accessors OK; blind data-bag pairs not).
- Naming conventions (predicate/`render_*`/`build_*`/`get_*`) tied to real repo usage.
- Complexity limits (method length, nesting, params, ternaries).
- Error-handling + boundary-validation guidance.
- Explicit AI-agent instructions in the governance doc.

## Reusable snippets

- The nested-ternary → early-return pattern (SKILL + examples #1/#3) is the canonical
  "clean this up" template agents can apply generally.
- Subscriber → `$this->container->get( Service::class )` delegation is the canonical hook
  snippet (examples #6–#9).
- `Controller::factory([...])` + thin render template is the canonical block snippet
  (examples #15).

## Reusable checklists

- Short review list in `SKILL.md` (fast path).
- Full review checklist + acceptance criteria in `governance-template.md` (PR path).
- Consider extracting the checklist into a `checklist.md` if teams want to attach only that.

## Rule prioritization

Tie-break order when guidance seems to conflict:

1. `core` codebase conventions (nearest existing example wins).
2. This skill (`SKILL.md`).
3. `governance-template.md`.
4. General WordPress/PHP/OOP best practice.

Highest-value rules to enforce first: no nested ternaries; thin templates / logic in
controllers; hooks delegate to services; no ungrounded abstraction; type + escape.

## Possible split into smaller modules

- `modernpress-coding-standards` (core style + OOP + review) — the default attach.
- `modernpress-blocks` (block controllers, render templates, wrapper attrs).
- `modernpress-cpts-and-queries` (CPT trio, query area, loops).
- `modernpress-integrations` (third-party subscriber/service pattern).

Only split if `SKILL.md` grows past comfortable attach size; today one skill + companions is
enough and avoids fragmentation.

## Prompt compression ideas

- Lead with the "Golden rules" block so a truncated read still captures the essentials.
- Encode discouraged patterns as short imperatives ("no nested ternaries", "no logic in
  render.php") that map cleanly to review comments.
- Keep examples numbered so prompts/reviews can cite "examples #1" instead of pasting code.

## Examples to add later

- Real trait extraction vs premature trait.
- Value object / DTO used legitimately (a case that meets an abstraction trigger).
- Meta / ACF field access via a config constant vs magic string.
- A PHPStan-level-5 failure and its fix (typed return, null-safety).
- Block variation / block binding controller example.

## Testing / validation ideas

- Add a tiny "definition of done": `composer phpcs` + `composer phpstan` clean, checklist
  passed. (Referenced in SKILL/governance.)
- Periodically re-derive conventions from `core` (grep for `factory(`, `Abstract_Subscriber`,
  `container->get(`) so the skill can't drift from the codebase.
- Optionally lint the skill's own PHP snippets against `phpcs.xml.dist` to keep examples
  standard-compliant.
- Spot-check the skill by having an agent refactor a known-bad snippet (the a11y ternary) and
  confirm it produces the early-return/extracted-method form.

## Risks & tradeoffs

- **Drift**: the skill can fall out of sync with `core`. Mitigation: codebase-wins rule +
  periodic re-derivation.
- **Over-prescription**: too many hard limits push agents toward mechanical output.
  Mitigation: limits are guidelines with a "note the reason if exceeded" escape hatch.
- **Getters nuance**: the "avoid getters/setters" rule can be misread as banning the repo's
  accessor style. Mitigation: the explicit intent-revealing-accessor carve-out.
- **Split cost**: more modules = more maintenance and attach decisions. Keep consolidated
  until size forces a split.
- **Companion docs not loaded**: agents may not open companions. Mitigation: the essential
  rules and the anchor example live in `SKILL.md` itself.
