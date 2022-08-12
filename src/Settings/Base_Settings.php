<?php declare(strict_types=1);

namespace Tribe\Libs\Settings;

/**
 * Class Base_Settings
 *
 * @package Tribe\Lib\Settings
 */
abstract class Base_Settings implements Settings_Builder {

	protected string $slug = '';

	public function __construct() {
		$this->set_slug();
	}

	/**
	 * @param int $priority
	 */
	public function hook( int $priority = 10 ): void {
		add_action( 'init', [ $this, 'register_settings' ], $priority, 0 );
	}

	/**
	 * Generates a unique-ish slug for this settings screen
	 */
	protected function set_slug(): void {
		$this->slug = sanitize_title( $this->get_parent_slug() . '-' . $this->get_title() );
	}

}
