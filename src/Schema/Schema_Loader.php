<?php declare(strict_types=1);

namespace Tribe\Libs\Schema;

class Schema_Loader {

	/**
	 * @var class-string<\Tribe\Libs\Schema\Schema>[]
	 */
	private array $schema_classes;

	/**
	 * @param class-string<\Tribe\Libs\Schema\Schema>[] $schema_classes A list of schema classes.
	 */
	public function __construct( array $schema_classes = [] ) {
		$this->schema_classes = $schema_classes;
	}

	/**
	 * @param class-string<\Tribe\Libs\Schema\Schema> $schema_class
	 */
	public function add( string $schema_class ): void {
		$this->schema_classes[] = $schema_class;
	}

	public function hook(): void {
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			return;
		}

		add_action( 'admin_init', [ $this, 'load_schema_updates' ], 10, 0 );
	}

	/**
	 * Load any expected updates for the given schemata
	 */
	public function load_schema_updates(): void {
		foreach ( $this->schema_classes as $classname ) {
			/** @var \Tribe\Libs\Schema\Schema $schema */
			$schema = new $classname;

			if ( ! $schema->update_required() ) {
				continue;
			}

			$schema->do_updates();
		}
	}

}
