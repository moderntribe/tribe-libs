# ModernPress PHP Governance

> Governance for PHP in the ModernPress core plugin/theme (`wp-content/plugins/core`,
> `wp-content/themes/core`). Adapted from a generic PHP governance seed to ModernPress and
> Modern Tribe conventions. `SKILL.md` is the short guide; this is the reference.
> When this document and the actual `core` codebase conflict, the codebase wins.

## Purpose

Keep ModernPress PHP simple, consistent, and reviewable. Give engineers and AI agents one
authoritative reference so changes match the existing architecture instead of introducing
new patterns, and so reviews are fast and predictable.

## Scope

- **Applies to:** PHP in `wp-content/plugins/core`, `wp-content/themes/core`, and
  `wp-content/mu-plugins`.
- **Out of scope:** vendor code, build output (`dist/`), generated files, JS/PCSS style
  (covered by ESLint/Prettier/Stylelint), infra/CI config.

## Design principles

1. **Match the codebase first.** Find the nearest existing example and follow it.
2. **Simplest thing that works.** Solve the current requirement, not an imagined one.
3. **Readable over clever.** Explicit control flow beats compressed expressions.
4. **Single responsibility.** One reason to change per class and per method.
5. **Explicit dependencies.** Inject via the container; no hidden globals or service locators.
6. **Type + escape.** Everything typed; every dynamic output escaped for its context.
7. **Abstraction earns its place.** Every interface/factory/trait/DTO needs a concrete reason.

## ModernPress expectations

- `<?php declare(strict_types=1);` first line, every file. PHP 8.4 features are available.
- Namespaces: `Tribe\Plugin\*` (plugin), `Tribe\Theme\*` (theme); PSR-4 autoload, no manual
  `require`/`include` chains.
- **Subscribers** own hook registration: extend `Tribe\Plugin\Core\Abstract_Subscriber`,
  implement `register(): void`, and delegate work to services via
  `$this->container->get( Service::class )->method()`. Never place business logic inline in
  a hook closure beyond a thin delegation.
- **Controllers** prepare data for templates: extend `Abstract_Controller` /
  `Abstract_Block_Controller`, built with `Controller::factory([...])` (PHP-DI `make()`).
  Expose intent-revealing accessors; keep raw attribute wrangling inside the controller.
- **Render/template files** are presentation only: factory a controller,
  `get_block_wrapper_attributes()`, simple loops/conditionals, escaped output.
- **CPTs** use the trio: a `Post_Object` subclass with a `NAME` constant, a `Config`
  extending the post-type config base, and a subscriber extending `Post_Type_Subscriber`
  with `protected string $config_class`. Model new CPTs on `src/Post_Types/Training`.
- **Query logic** lives in the plugin `Query` area (`pre_get_posts`, query-var filters,
  loop filters); a one-off `WP_Query` may live in a block controller when private to it.
- **Integrations** (`src/Integrations`): hooks in `Integrations_Subscriber`, behavior in
  focused per-service classes.

## Modern Tribe coding expectations

- Standard: `moderntribe/coding-standards` (`ModernTribe` PHPCS ruleset). Auto-fix with
  `composer phpcbf`; verify with `composer phpcs`.
- Tabs for indentation (width 4); spaces inside parentheses `foo( $bar )`; align `=>`/`=`
  where it improves readability; short arrays.
- Naming: classes `Pascal_Snake_Case`; methods/properties `snake_case`; constants
  `UPPER_SNAKE`. Booleans read as questions (`has_*`, `is_*`, `does_*`, `can_*`); string
  builders `render_*`; heavy builders `build_*`; cached accessors `get_*`.
- Text domain `'tribe'` for all i18n functions.
- Static analysis: PHPStan **level 5** must pass (`composer phpstan`).

## OOP guidance

Prefer:

- early returns / guard clauses over nested conditionals;
- small, single-purpose classes and methods;
- composition and focused traits over inheritance;
- constructor injection and explicit dependencies;
- behavior-rich objects (methods that answer questions and render, not just hold data);
- intent-revealing accessors that expose only what callers need.

Avoid:

- nested inline ternaries and other compressed expressions;
- blind `get_`/`set_` pairs mirroring every private field on a behaviorless data bag;
- God classes and long methods mixing responsibilities;
- inheritance, interfaces, traits, or factories introduced without a present need;
- static utility classes replacing dependency injection;
- hidden side effects and redundant defensive checks.

## WordPress integration guidance

- Prefer the standard loop (`while ( $query->have_posts() ) { $query->the_post(); }`) when
  template context is needed.
- Avoid `get_posts()` + custom `foreach` clobbering global `$post`; avoid `setup_postdata()`
  in custom loops. Inside custom loops use ID-accepting helpers
  (`get_the_title( $post_id )`, `get_the_content( null, false, $post_id )`).
- Escape at output with the context-correct function: `esc_html`, `esc_attr`, `esc_url`,
  `esc_html__`, `esc_attr__`, `wp_kses`. Use `wp_json_encode` for embedded JSON.
- Validate values crossing the WP boundary (hook args typed `mixed`), e.g.
  `if ( ! is_array( $x ) ) { return []; }` — but don't re-check types already guaranteed.
- `// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped` only for markup that is
  already escaped/trusted (wrapper attributes, rendered inner blocks). Never to silence a
  genuinely unescaped value.
- Use `get_block_wrapper_attributes()` for block wrappers.

## Complexity limits

Guidelines, not hard gates — exceed them only with a clear reason noted in review.

- Method length: aim ≤ ~30 lines; extract when a method grows past one clear job.
- Nesting depth: ≤ 2 levels; use guard clauses to flatten.
- Ternaries: never nested; a single simple `?:`/`??` is fine.
- Parameters: ≤ ~4; pass an args array or object beyond that (matches `factory([...])`).
- Class size: if a class needs "and" to describe it, split it.
- Cyclomatic complexity per method: keep low; branch-heavy logic → extract predicates.

## Preferred patterns

- Subscriber → container service delegation for all hooks.
- `Controller::factory([...])` for view models; thin render templates.
- Focused traits for genuinely shared behavior (e.g. `Primary_Term`, `Post_Data`).
- Interfaces + implementations where real polymorphism exists (`Rule_Interface`,
  `Meta_Box_Handler_Interface`).
- Factories where a decision selects among real implementations (`Filter_Factory`).
- Guard clauses; `null`/`false`/`[]` returns for "no data" in template paths.

## Discouraged patterns

- Business logic in `render.php` / template files.
- New static methods (outside established `factory()`/bootstrap patterns).
- Service locators or global state instead of injection.
- Single-implementation interfaces/factories; "just in case" abstractions.
- Nested ternaries; deeply nested conditionals.
- Copy-pasted logic instead of a shared method/trait.
- `get_posts()` loops that override the global `$post`.
- Unescaped output; blanket PHPCS ignores.

## Code review checklist

- [ ] `declare(strict_types=1);` present; everything typed (props, params, returns).
- [ ] Follows an existing `core` pattern; no ungrounded new abstraction.
- [ ] Hooks live in a subscriber and delegate to a service.
- [ ] Controllers hold logic; templates only present escaped data.
- [ ] No nested ternaries; early returns used; nesting ≤ 2.
- [ ] Each class/method single-purpose with an intent-revealing name.
- [ ] No blind getter/setter data bags; accessors expose only what's needed.
- [ ] All dynamic output escaped with the correct `esc_*`; ignores justified.
- [ ] Query logic in the Query area; standard loop used where context is needed.
- [ ] CPTs follow the model/config/subscriber trio.
- [ ] No dependency changes without explicit approval.
- [ ] `composer phpcs` and `composer phpstan` pass (scoped runs OK for narrow edits).

## AI-agent instructions

1. Before writing code, locate the nearest existing example in `core` and mirror it.
2. Default to the smallest change that satisfies the request. If tempted to add an
   interface/factory/trait/DTO, first confirm it meets an "add abstraction" trigger; if not,
   don't add it and say why.
3. Never introduce a nested ternary. Produce early-return code or extract a named method.
4. Keep hooks in subscribers and delegate to container services; do not inline logic.
5. Keep render/template files thin; move computation into a controller.
6. Escape all output; add PHPCS ignores only for genuinely pre-escaped markup, with a
   trailing comment.
7. Do not run package managers, install/upgrade dependencies, or touch infra/CI.
8. When practical, mention the scoped `composer phpcs`/`composer phpstan` commands you would
   run; do not fabricate results.
9. If a requirement conflicts with these rules, follow the codebase and flag the conflict.

## Examples

See `examples.md` for paired bad/good snippets covering: nested-ternary replacement, early
returns, method extraction, class responsibility, unnecessary getters/setters, dependency
injection, service usage, WordPress hooks, filters/actions, conditionals, naming,
unnecessary abstraction, overengineering, and understructured code.

## Acceptance criteria

A change is acceptable when:

- it matches existing ModernPress architecture and Modern Tribe style;
- it is the simplest implementation that satisfies the requirement, with no ungrounded
  abstraction;
- it contains no nested ternaries and reads top-to-bottom with early returns;
- classes and methods are single-purpose and clearly named;
- everything is typed and all output is escaped;
- hooks delegate to services and templates stay thin;
- the review checklist passes and `composer phpcs` / `composer phpstan` are clean;
- no dependencies or infrastructure changed without explicit approval.
