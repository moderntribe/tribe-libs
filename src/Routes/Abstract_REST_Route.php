<?php declare(strict_types=1);

namespace Tribe\Libs\Routes;

/**
 * Base class for REST API endpoints to extend.
 *
 * @package Tribe\Project\Routes
 */
abstract class Abstract_REST_Route {

	/**
	 * Registers the route with WP lifecycle hooks.
	 */
	abstract public function register(): void;

	/**
	 * Returns the project namespace.
	 *
	 * @return string The project's namespace.
	 */
	public function get_project_namespace(): string {
		return 'tribe/v1';
	}

}
