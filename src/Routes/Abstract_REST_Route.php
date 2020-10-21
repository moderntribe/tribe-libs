<?php // phpcs:disable WordPress.Files.FileName.NotHyphenatedLowercase
/**
 * Base class for REST API endpoints.
 *
 * @package Tribe\Project\Routes
 */

declare( strict_types=1 );

namespace Tribe\Libs\Routes;

/**
 * Base class for REST API endpoints to extend.
 */
abstract class Abstract_REST_Route {
	/**
	 * Determines if the object should be registered.
	 *
	 * @return bool True if the object should be registered, false otherwise.
	 */
	public function can_register() : bool {
		return true;
	}

	/**
	 * Registers the route with WP lifecycle hooks.
	 *
	 * @return void
	 */
	abstract public function register() : void;

	/**
	 * Returns the WP API namespace.
	 *
	 * @return string The WP API namespace.
	 */
	public function get_api_namespace() : string {
		return 'wp/v2';
	}

	/**
	 * Returns the project namespace.
	 *
	 * @return string The project's namespace.
	 */
	public function get_project_namespace() : string {
		return 'tribe/v1';
	}
}
