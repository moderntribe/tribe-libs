# ModernPress Good/Bad Examples

Paired snippets for the rules in `SKILL.md` / `governance-template.md`. Each shows a **Bad**
version, a **Good** version, and **why**. Snippets are ModernPress-flavored (Modern Tribe
style, `Tribe\Plugin\*`, tabs, `snake_case` methods). Names are illustrative.

---

## 1. Nested ternary → early-return assignment

Bad:

```php
echo esc_attr( $c->get_link_a11y_label() ?: ( $c->has_link_text() ? sprintf( __( '%1$s: %2$s', 'tribe' ), $c->get_link_text(), $c->get_title() ) : sprintf( __( 'Read more: %s', 'tribe' ), $c->get_title() ) ) );
```

Good:

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

Why: the bad version hides three branches in one unreadable line that is hard to diff,
debug, or extend. The good version reads top-to-bottom, and each rule is independently
visible. Best of all, this belongs in a controller method (see #3).

---

## 2. Early returns / guard clauses

Bad:

```php
public function passes( \WP_Post $announcement, array $context ): bool {
	if ( $this->is_enabled( $announcement ) ) {
		if ( $this->has_window( $announcement ) ) {
			if ( $this->within_window( $announcement, $context ) ) {
				return true;
			} else {
				return false;
			}
		} else {
			return true;
		}
	} else {
		return true;
	}
}
```

Good:

```php
public function passes( \WP_Post $announcement, array $context ): bool {
	if ( ! $this->is_enabled( $announcement ) ) {
		return true;
	}

	if ( ! $this->has_window( $announcement ) ) {
		return true;
	}

	return $this->within_window( $announcement, $context );
}
```

Why: guard clauses flatten nesting and remove `else` noise. The exit conditions are obvious
and the method matches the repo's rule classes.

---

## 3. Method extraction (logic out of the template)

Bad — logic in `render.php`:

```php
<?php
$label = $c->get_link_a11y_label();
if ( ! $label && $c->has_link_text() ) {
	$label = sprintf( __( '%1$s: %2$s', 'tribe' ), $c->get_link_text(), $c->get_title() );
}
if ( ! $label ) {
	$label = sprintf( __( 'Read more: %s', 'tribe' ), $c->get_title() );
}
?>
<a href="<?php echo esc_url( $c->get_link_url() ); ?>" aria-label="<?php echo esc_attr( $label ); ?>">
```

Good — controller owns the logic, template just echoes:

```php
// Abstract_Card_Controller
public function get_link_a11y_label(): string {
	if ( '' !== $this->link_a11y_label ) {
		return $this->link_a11y_label;
	}

	if ( '' !== $this->link_text ) {
		return sprintf( __( '%1$s: %2$s', 'tribe' ), $this->link_text, $this->title );
	}

	return sprintf( __( 'Read more: %s', 'tribe' ), $this->title );
}
```

```php
<a href="<?php echo esc_url( $c->get_link_url() ); ?>" aria-label="<?php echo esc_attr( $c->get_link_a11y_label() ); ?>">
```

Why: templates should present, not compute. Moving the fallback logic into a named method
makes it testable, reusable across desktop/mobile markup, and keeps `render.php` thin.

---

## 4. Class responsibility (avoid God classes)

Bad — one class does everything:

```php
class Announcement_Block_Controller extends Abstract_Block_Controller {
	public function should_display(): bool {
		// scheduling + placement + front-page + page-visibility logic all inline...
	}
}
```

Good — one rule per class behind an interface:

```php
interface Rule_Interface {
	public function passes( \WP_Post $announcement, array $context ): bool;
	public function get_name(): string;
}

class Scheduling_Rule implements Rule_Interface { /* ... */ }
class Placement_Rule  implements Rule_Interface { /* ... */ }
```

Why: mixing every display rule in one method makes it long, branch-heavy, and untestable.
The repo already splits these into `Rules/` — a legitimate interface because multiple real
implementations exist (see #13 for when *not* to add an interface).

---

## 5. Unnecessary getters/setters vs intent-revealing accessors

Bad — behaviorless data bag with a get/set pair per field:

```php
class Card_Data {
	private string $title = '';
	private string $media_url = '';
	public function set_title( string $t ): void { $this->title = $t; }
	public function get_title(): string { return $this->title; }
	public function set_media_url( string $u ): void { $this->media_url = $u; }
	public function get_media_url(): string { return $this->media_url; }
}
```

Good — a controller that answers questions and does work:

```php
class Image_Card_Controller extends Abstract_Card_Controller {
	public function has_media(): bool {
		return 0 !== $this->media_id || '' !== $this->media_url;
	}

	public function get_media( string $image_size = 'large' ): string {
		if ( 0 !== $this->media_id ) {
			return wp_get_attachment_image( $this->media_id, $image_size );
		}

		return $this->media_url ? '<img src="' . esc_url( $this->media_url ) . '" alt="">' : '';
	}
}
```

Why: raw get/set pairs add noise and no value. ModernPress controllers instead expose
predicates (`has_media()`) and computed output (`get_media()`) — accessors that carry
behavior and hide state. Set data once in the constructor from attributes, not via setters.

---

## 6. Dependency injection vs hidden construction

Bad — subscriber news up collaborators and reaches for globals:

```php
class Integrations_Subscriber extends Abstract_Subscriber {
	public function register(): void {
		$yoast = new YoastSEO();
		add_filter( 'wpseo_accessible_post_types', [ $yoast, 'exclude_post_types' ], 100 );
	}
}
```

Good — resolve through the injected container:

```php
class Integrations_Subscriber extends Abstract_Subscriber {
	public function register(): void {
		add_filter( 'wpseo_accessible_post_types', function ( $post_types ) {
			if ( ! is_array( $post_types ) ) {
				return [];
			}

			return $this->container->get( YoastSEO::class )->exclude_post_types( $post_types );
		}, 100 );
	}
}
```

Why: the container manages lifecycles and makes dependencies explicit and swappable/testable.
`new`-ing collaborators inside a subscriber hides dependencies and breaks the DI convention.

---

## 7. Service usage (delegate, don't inline)

Bad — post-type registration logic inline in the hook:

```php
add_action( 'init', function (): void {
	register_post_type( 'training', [ /* many args inline */ ] );
	// caps, templates, list-table tweaks all here too...
}, 0 );
```

Good — subscriber delegates to the Config service:

```php
public function register(): void {
	parent::register(); // registers the CPT via $this->config_class

	add_action( 'init', function (): void {
		$this->container->get( Config::class )->register_block_template();
	} );
}
```

Why: subscribers wire hooks; services do work. This keeps registration testable and matches
`Training_Subscriber` + `Config`.

---

## 8. WordPress hooks (thin closures)

Bad — heavy logic embedded in the closure:

```php
add_filter( 'render_block', function ( $content, $parsed, $block ) {
	if ( 'core/paragraph' === $parsed['blockName'] ) {
		// 30 lines of DOM munging inline...
	}
	return $content;
}, 10, 3 );
```

Good — closure delegates to a service/factory:

```php
add_filter( 'render_block', function ( string $content, array $parsed, object $block ): string {
	$filter = $this->container->get( Filter_Factory::class )->make( $parsed );

	return $filter ? $filter->filter_block_content( $content, $parsed, $block ) : $content;
}, 10, 3 );
```

Why: typed closure signature + delegation keeps the subscriber scannable and moves logic
where it can be reused and tested.

---

## 9. Filters/actions (return types & priorities)

Bad:

```php
add_filter( 'rank_math/excluded_post_types', function ( $types ) {
	return array_merge( $types, [ 'training' ] );
} );
```

Good:

```php
add_filter( 'rank_math/excluded_post_types', function ( $post_types ) {
	if ( ! is_array( $post_types ) ) {
		return [];
	}

	return $this->container->get( RankMath::class )->exclude_post_types( $post_types );
}, 100 );
```

Why: validate the boundary value (WP may pass non-arrays), delegate the behavior, and set an
explicit priority. Reference constants (e.g. `Training::NAME`) instead of magic strings.

---

## 10. Conditionals (predicates over inline comparisons)

Bad:

```php
if ( $this->show_footer_ctas && ( $this->cta_placement === 'header' ) && ! empty( $this->columns[ $i ]['ctaUrl'] ) && ! empty( $this->columns[ $i ]['ctaLabel'] ) ) {
	// ...
}
```

Good:

```php
public function ctas_in_header(): bool {
	return $this->show_footer_ctas() && 'header' === $this->cta_placement();
}

public function has_column_cta( int $index ): bool {
	return '' !== $this->get_column_cta_label( $index ) && '' !== $this->get_column_cta_url( $index );
}

if ( $this->ctas_in_header() && $this->has_column_cta( $i ) ) {
	// ...
}
```

Why: named predicates make the intent obvious, are reusable, and read like prose. Yoda
comparisons (`'header' === $x`) match the ModernTribe standard.

---

## 11. Naming

Bad:

```php
public function proc( int $i ): string { /* builds a column header */ }
public function chk( int $i ): bool { /* has a CTA? */ }
```

Good:

```php
public function render_table_column_header( int $index ): string { /* ... */ }
public function has_column_cta( int $index ): bool { /* ... */ }
```

Why: names should state intent and follow the repo's verb conventions — `render_*` returns
markup, `has_*` returns bool. Abbreviations like `proc`/`chk` force readers to open the body.

---

## 12. Query loop

Bad:

```php
$data = get_posts( [ 'post_type' => 'training' ] );
foreach ( $data as $post ) {
	setup_postdata( $post );
	the_title();
}
wp_reset_postdata();
```

Good:

```php
$query = new WP_Query( [ 'post_type' => Training::NAME ] );

while ( $query->have_posts() ) {
	$query->the_post();
	the_title();
}

wp_reset_postdata();
```

Or, without touching global state:

```php
foreach ( $post_ids as $post_id ) {
	echo esc_html( get_the_title( $post_id ) );
}
```

Why: overriding the global `$post` with `get_posts()` + `setup_postdata()` is fragile. Use
the standard loop when you need template context, or ID-accepting helpers when you don't.

---

## 13. Unnecessary abstraction

Bad — an interface with exactly one implementation, added "for flexibility":

```php
interface Post_Title_Formatter_Interface {
	public function format( int $post_id ): string;
}

class Default_Post_Title_Formatter implements Post_Title_Formatter_Interface {
	public function format( int $post_id ): string {
		return esc_html( get_the_title( $post_id ) );
	}
}
```

Good — just do the thing:

```php
public function get_post_title(): string {
	return $this->post_title; // set once in set_post()
}
```

Why: an interface/factory with a single obvious implementation adds indirection with no
payoff. Add the interface later, when a *second* real implementation actually appears.

---

## 14. Overengineered code

Bad — a factory + registry + config object to pick a CSS class:

```php
$resolver = new Layout_Class_Resolver( new Layout_Config_Repository() );
$class    = $resolver->resolve( $this->attributes );
```

Good:

```php
$this->layout        = $this->attributes['layout'] ?? 'vertical';
$this->block_classes .= " c-post-card--layout-{$this->layout}";
```

Why: not every value needs a pattern. A default + string concatenation in the constructor is
clear and matches `Post_Card_Controller`. Reserve patterns for genuine variation.

---

## 15. Understructured code (add the missing structure)

Bad — everything crammed into one render file:

```php
<?php
// render.php: reads attributes, builds rows, computes headers, loops columns,
// formats CTAs, and echoes ~150 lines of mixed logic and markup.
```

Good — a controller prepares data, the template presents it:

```php
$c = Comparison_Table_Block_Controller::factory( [
	'attributes'    => $attributes,
	'block'         => $block,
	'block_classes' => 'b-comparison-table',
] );
?>
<figure <?php echo $wrapper_attrs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<?php foreach ( $c->get_row_controllers() as $row ) : ?>
		<?php echo $row->render_row(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	<?php endforeach; ?>
</figure>
```

Why: simplicity is not "no structure." When a template grows real logic, the fix is to add
the *right* structure (a controller), not to abstract prematurely or to leave a 150-line
render file. Balance: enough structure to keep responsibilities clear, no more.
