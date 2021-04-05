<?php declare( strict_types=1 );
/**
 * Base class for REST API endpoints.
 *
 * @package Tribe\Project\Routes
 */

namespace Tribe\Libs\Routes;

/**
 * Base class for REST API endpoints to extend.
 */
abstract class Abstract_REST_Route {
	/**
	 * Registers the route with WP lifecycle hooks.
	 *
	 * @return void
	 */
	abstract public function register(): void;

	/**
	 * Returns the WP API namespace.
	 *
	 * @return string The WP API namespace.
	 */
	public function get_api_namespace(): string {
		return 'wp/v2';
	}

	/**
	 * Returns the project namespace.
	 *
	 * @return string The project's namespace.
	 */
	public function get_project_namespace(): string {
		return 'tribe/v1';
	}
}
