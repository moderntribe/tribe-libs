<?php declare(strict_types=1);

namespace Tribe\Libs\Routes;

use WP;

/**
 * Class to register routers for normal and REST API endpoints.
 *
 * @package Tribe\Project\Routes
 */
class Router_Factory {

	/**
	 * Instance of the rewrite rule manager.
	 */
	public ?Router_Rule_Manager $manager;

	/**
	 * List of Route Instances, indexed by their regex pattern.
	 *
	 * @var array<string, \Tribe\Libs\Routes\Abstract_Route>
	 */
	protected array $routes;

	public function __construct( ?Router_Rule_Manager $manager = null ) {
		$this->manager = $manager;
	}

	/**
	 * Looks up the custom route for the pattern matched. Returns false
	 * if not found.
	 *
	 * @param string $pattern           The regex pattern to lookup.
	 * @param array  $registered_routes Routes registered.
	 *
	 * @return \Tribe\Libs\Routes\Abstract_Route|null The route or null on failure.
	 */
	public function find_route( string $pattern, array $registered_routes ): ?Abstract_Route {
		// Load routes if not already loaded.
		if ( empty( $this->routes ) ) {
			$this->routes = $this->manager->get_route_objects( $registered_routes );
		}

		$route = $this->routes[ $pattern ] ?? null;

		return $route ?: null;
	}

	/**
	 * Checks if the current route is a custom route and activates it if matched.
	 *
	 * @action parse_request
	 *
	 * @param \WP   $wp                The global wp object.
	 * @param array $registered_routes Routes registered.
	 *
	 * @return \Tribe\Libs\Routes\Abstract_Route|null The matched route on success, null on failure.
	 */
	public function did_parse_request( WP $wp, array $registered_routes ): ?Abstract_Route {
		$pattern       = $wp->matched_rule;
		$matched_route = $this->find_route( $pattern, $registered_routes );

		return $matched_route ?: null;
	}

}
