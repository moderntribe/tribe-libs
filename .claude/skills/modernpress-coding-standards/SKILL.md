---
name: modernpress-coding-standards
description: Use when writing or reviewing PHP in wp-content/plugins/core or wp-content/themes/core (ModernPress). Covers Modern Tribe PHPCS/PHPStan style, architecture (subscribers, controllers, CPTs, query hooks, integrations, blocks), pragmatic OOP, and readable, reviewable code.
disable-model-invocation: true
---

# ModernPress Coding Standards

Write simple, readable, reviewable PHP that follows the **existing** ModernPress
architecture and Modern Tribe style. Match the codebase before inventing patterns.
Prefer the simplest solution that meets the current requirement.

Primary paths: `wp-content/plugins/core`, `wp-content/themes/core`.

## Activation

- Do **not** apply this skill automatically on every prompt. Apply it only when the user
  explicitly requests it (e.g. names the skill or asks for ModernPress standards / a review).
- If a request looks like ModernPress PHP work but the user hasn't asked for the skill, ask
  first whether to apply it before following its rules — don't assume.

## Golden rules

1. Follow existing patterns in `core` before adding a new one.
2. Simplest thing that works; add abstraction only for a concrete reason (see below).
3. Readable over clever: no nested inline ternaries, use early returns.
4. One responsibility per class and per method.
5. Type everything; escape at output; delegate hooks to services.

## Baseline style

- Standards: `moderntribe/coding-standards` (`ModernTribe` ruleset) + repo `phpcs.xml.dist`;
  static analysis at PHPStan **level 5**. PHP **8.4**.
- `<?php declare(strict_types=1);` as the first statement in every PHP file.
- Modern PHP: short arrays `[]`, typed properties/params/return types, union types
  (`int|null`, `\WP_Term|null`), namespaces, narrowest visibility.
- Formatting: tabs (width 4); spaces inside `( ... )`; align `=>` and `=` when it aids
  reading; contextual escaping (`esc_html`, `esc_attr`, `esc_url`, `esc_*__`).
- Naming: classes `Pascal_Snake_Case`; methods/properties `snake_case`; constants
  `UPPER_SNAKE`. Predicates `has_*`/`is_*`/`does_*`/`can_*` return `bool`; string builders
  `render_*`; heavy builders `build_*`, cached accessors `get_*`.
- Prefer instance methods + dependency injection. Do **not** add new static methods except
  to match an established framework pattern (e.g. `factory()`, bootstrap singleton).
- For WordPress APIs, treat the installed WP version + https://developer.wordpress.org/reference/
  as the source of truth.

## Architecture

- **Subscribers** register hooks. Extend `Tribe\Plugin\Core\Abstract_Subscriber`
  (container injected via constructor), implement `register(): void`. Put `add_action`/
  `add_filter` here; delegate work to services: `$this->container->get( Service::class )->do()`.
- **Controllers** prepare template data. Extend `Abstract_Controller` (or
  `Abstract_Block_Controller` for blocks); build them with `Controller::factory([...])`,
  which resolves through the PHP-DI container. Expose intent-revealing accessors
  (`has_media()`, `get_media()`, `get_title()`).
- **Render/template files** stay thin: factory a controller, compute wrapper attributes,
  do simple loops/conditionals, output escaped markup. No business logic here.
- **Config classes** hold configuration; **models** (`Post_Object` subclasses) hold a
  `NAME` constant and post data access.
- Keep each unit single-purpose: a `*validate*` method only validates — it must not also
  normalize or mutate. Extract repeated behavior into a reusable service/controller method
  or focused trait that matches the existing structure (e.g. reuse
  `Tribe\Plugin\Components\Traits\Primary_Term` for primary-term lookups).

## Pragmatic OOP & simplicity

**Prefer:** early returns / guard clauses; small focused classes and methods; composition
over inheritance; constructor injection; explicit dependencies; behavior-rich objects;
clear names; simple conditionals.

**Avoid:** nested inline ternaries; blind getter/setter pairs on data-bag classes; God
classes; unnecessary inheritance/interfaces/traits; factories with a single obvious
implementation; abstractions added "just in case"; long mixed-responsibility methods;
hidden side effects; defensive checks that duplicate guarantees the code already has.

**Getters/setters:** intent-revealing accessors on controllers/read-models are the
ModernPress convention and are fine. What to avoid is auto-generating a `get_`/`set_` pair
for every private field on a class with no behavior — expose only what callers need, and
prefer predicates + computed values over raw state.

### Add abstraction only if at least one is true

- multiple implementations exist **now**;
- real duplication already exists;
- it clearly improves testability;
- it matches an existing ModernPress convention;
- it creates a genuine architectural boundary;
- a concrete, known (not hypothetical) future extension requires it.

Real precedents in the repo: interfaces with multiple impls (`Rule_Interface`,
`Meta_Box_Handler_Interface`) and factories with real polymorphism (`Filter_Factory`,
meta-box handlers). Do not invent new interfaces/factories/DTOs/value objects without a
reason like these.

## Readability: no nested ternaries

Deeply nested inline ternaries are the top thing to eliminate. Replace with early-return
assignment or an extracted, well-named method.

Avoid:

```php
echo esc_attr( $c->get_link_a11y_label() ?: ( $c->has_link_text() ? sprintf( __( '%1$s: %2$s', 'tribe' ), $c->get_link_text(), $c->get_title() ) : sprintf( __( 'Read more: %s', 'tribe' ), $c->get_title() ) ) );
```

Prefer:

```php
$label = $c->get_link_a11y_label();

if ( ! $label && $c->has_link_text() ) {
	$label = sprintf( __( '%1$s: %2$s', 'tribe' ), $c->get_link_text(), $c->get_title() );
}

if ( ! $label ) {
	$label = sprintf( __( 'Read more: %s', 'tribe' ), $c->get_title() );
}

echo esc_attr( $label );
```

Better still, move that logic into a `get_link_a11y_label()` (or similar) method on the
controller so the template just echoes one escaped value. More patterns in `examples.md`.

## Blocks

- Do not put block logic in `render.php`. Keep templates thin (see Architecture).
- Trivial attribute reads (≈1–6 lines) can stay in the template. Anything that transforms
  attributes, branches, loops meaningfully, or computes values belongs in a controller.
- Dynamic block controllers extend `Tribe\Plugin\Components\Abstracts\Abstract_Block_Controller`;
  follow existing controllers in `wp-content/plugins/core/src/Components/Blocks`.
- Theme block PHP usually extends `Tribe\Plugin\Blocks\Block_Base` (core-block extensions)
  or uses plugin controllers for dynamic `tribe/*` render templates.
- Use `get_block_wrapper_attributes()` for wrappers. Use PHPCS escape-output ignores only
  for intentionally safe pre-escaped markup (wrapper attrs, rendered inner blocks).

## Custom Post Types

- Model new CPTs on existing custom CPTs, especially `src/Post_Types/Training` — not on
  Page or Post.
- Use the trio: `Post_Object` subclass with a `NAME` constant; `Config` extending the
  post-type config base; a subscriber extending `Post_Type_Subscriber` with
  `protected string $config_class`.
- Keep labels, args, templates, permissions, and list-table behavior in that structure.

## Query changes

- Put query behavior (`pre_get_posts`, query-var filters, archive/search filtering, query
  loop filters) in the plugin `Query` area. Do not scatter it through render templates,
  theme templates, or CPT classes. A one-off local `WP_Query` may live in a block
  controller when private to that block.
- Prefer the standard loop when template context is needed:

```php
while ( $query->have_posts() ) {
	$query->the_post();
}
```

- Avoid `get_posts()` + custom `foreach` that overrides global `$post`, and avoid
  `setup_postdata()` in such loops. Inside custom result loops, prefer ID-accepting helpers
  (`get_the_title( $post_id )`, `get_the_content( null, false, $post_id )`) over global
  template tags.

## Integrations

- Third-party code lives in `src/Integrations`. Hooks go in `Integrations_Subscriber`;
  behavior goes in focused classes (ACF, Yoast SEO, Rank Math). Validate values crossing
  the WP boundary (e.g. `if ( ! is_array( $x ) ) { return []; }`), but do not add redundant
  guards where the type is already guaranteed.

## Error handling & escaping

- Guard clauses and early returns over deep nesting. Return `null`/`false`/`[]` for absent
  data rather than throwing in template paths.
- Handle WP fallibility explicitly (`is_wp_error()`, `empty()`), and escape every dynamic
  value at the point of output using the correct `esc_*` function for the context.

## Code review expectations

Before proposing PHP as done, check it:

- follows an existing `core` pattern; adds no ungrounded abstraction;
- has no nested inline ternaries; uses early returns; conditionals are simple;
- keeps classes/methods single-purpose with clear names;
- types everything; escapes all output; delegates hooks to services;
- passes `composer phpcs` and `composer phpstan` (scoped commands acceptable for narrow edits).

Full checklist in `governance-template.md`.

## Validation

- Run repo checks for PHP work when practical: `composer phpcs`, `composer phpstan`
  (scoped runs OK for narrow edits). `composer phpcbf` auto-fixes many style issues.
- Never change dependencies without explicit user approval.

## Companion docs (read on demand)

- `governance-template.md` — ModernPress governance: scope, principles, complexity limits,
  preferred/discouraged patterns, review checklist, AI-agent rules, acceptance criteria.
- `examples.md` — realistic good/bad ModernPress PHP for each rule above.
- `optimization-plan.md` — how to keep this skill reliable, lean, and easy to attach.
