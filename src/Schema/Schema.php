<?php declare(strict_types=1);

namespace Tribe\Libs\Schema;

use Throwable;

/**
 * Class Schools
 *
 * @package Tribe
 *
 * A base class for handling schema updates. To use, extend and implement
 * the get_updates() method. Instantiate your subclass where appropriate to
 * do any required updates. Example:
 *
 * $my_schema = new My_Schema();
 * if ( $my_schema->update_required() ) {
 *   $my_schema->do_updates();
 * }
 */
abstract class Schema {

	/**
	 * Bump this in your child class each time you want to
	 * force your Schema to run.
	 *
	 * @var int|float|string
	 */
	protected $schema_version = 0;

	/**
	 * Defaults to the child class's with a -schema suffix.
	 */
	protected string $version_option = '';

	/**
	 * Returns an array of callbacks with numeric keys.
	 * Any key higher than the version recorded in the DB
	 * and lower than $this->schema_version will have its
	 * callback called.
	 *
	 * @return callable[]
	 */
	abstract protected function get_updates(): array;

	public function __construct() {
		$this->version_option = static::class . '-schema';
	}

	public function do_updates(): void {
		$this->clear_option_caches();
		$updates = $this->get_updates();
		ksort( $updates );

		try {
			foreach ( $updates as $version => $callback ) {
				if ( ! $this->is_version_in_db_less_than( $version ) ) {
					continue;
				}

				call_user_func( $callback );
			}

			$this->update_version_option( (string) $this->schema_version );
		} catch ( Throwable $e ) {
			// fail silently, but it should try again next time
		}
	}

	public function update_required(): bool {
		return $this->is_version_in_db_less_than( (string) $this->schema_version );
	}

	/**
	 * We've had problems with the notoptions and
	 * alloptions caches getting out of sync with the DB,
	 * forcing an eternal update cycle
	 */
	protected function clear_option_caches(): void {
		wp_cache_delete( 'notoptions', 'options' );
		wp_cache_delete( 'alloptions', 'options' );
	}

	protected function update_version_option( string $new_version ): void {
		update_option( $this->version_option, $new_version );
	}

	protected function is_version_in_db_less_than( string $version ): bool {
		$version_in_db = get_option( $this->version_option, '0' );

		return version_compare( $version, $version_in_db ) > 0;
	}

}
