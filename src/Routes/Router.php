<?php
/**
 * Class Router
 *
 * Class that is responsible for registering routes.
 *
 * @package Tribe\Project\Routes
 */

namespace Tribe\Libs\Routes;

use Tribe\Libs\Container\Abstract_Subscriber;

class Router extends Abstract_Subscriber {
	/**
	 * Currently matched route.
	 *
	 * @var string
	 */
	public $matched_route;

	/**
	 * List of Route Instances.
	 *
	 * @var array
	 */
	public $routes;

	/**
	 * List of router vars - tied to query vars.
	 *
	 * @var array
	 */
	public $router_vars;

	/**
	 * Class constructor.
	 *
	 * @param array $routes      WP routes to register.
	 * @param array $rest_routes REST routes to register.
	 * @return void
	 */
	public function register() : void {

	}

	/**
	 * The current router version. This should be bumped whenever
	 * changes are made to this file.
	 *
	 * @return string The current version of routes.
	 */
	public function get_version() : string {
		return '1.0.0';
	}

	/**
	 * Conditionally (soft) flushes rewrite rules. Ignored silently
	 * if the saved version in the DB is also the version in code.
	 *
	 * @return void
	 */
	public function flush_if_changed() : void {
		$version_in_code = $this->get_version();
		$version_in_db   = get_option( 'lib_router_version' );

		// Bail early if rules haven't changed.
		if ( $version_in_code !== $version_in_db ) {
			return;
		}

		$this->flush();
	}

	/**
	 * Wrapper to the WordPress's rewrite flushing API. Triggers the
	 * router_changed action on flush.
	 */
	public function flush() : void {
		flush_rewrite_rules();

		$version = $this->get_version();
		update_option( 'lib_router_version', $version );
	}


	/**
	 * Converts Route instances into Rewrite rules for adding to
	 * the WordPress rewrites
	 *
	 * @return array Rules for the route instances.
	 */
	public function get_rules() : array {
		$rules = [];

		foreach ( $this->get_route_objects() as $pattern => $route ) {
			// Skip routes that are defined in Core.
			if ( $route->is_core() ) {
				continue;
			}

			$matches  = $route->get_matches();
			$redirect = 'index.php';

			if ( ! empty( $matches ) ) {
				$redirect .= '?' . $this->get_redirect_params( $matches );
			}

			$rules[ $pattern ] = $redirect;
		}

		return $rules;
	}

	/**
	 * Converts query params array into a query string. Special $matches
	 * values are not URL encoded.
	 *
	 * @param array $params The query params.
	 * @return string       Query string.
	 */
	public function get_redirect_params( $params ) : string {
		$query_params = [];

		foreach ( $params as $key => $value ) {
			if ( false === strpos( $value, '$matches' ) ) {
				$query_params[] = urlencode( $key ) . '=' . urlencode( $value );
			} else {
				$query_params[] = urlencode( $key ) . '=' . $value;
			}
		}

		return implode( '&', $query_params );
	}

	/**
	 * Stores the route instances locally in an associative array on the
	 * regex pattern. Any custom query vars are also scanned and stored
	 * here.
	 *
	 * @return array Route instances.
	 */
	public function get_route_objects() : array {
		$this->init_routes();

		return $this->routes;
	}

	/**
	 * Lazy init routes and route vars.
	 *
	 * @return void
	 */
	public function init_routes() : void {
		// Bail early if routes are already defined.
		if ( ! empty( $this->routes ) ) {
			return;
		}

		$this->routes      = [];
		$this->router_vars = [];

		foreach ( $this->container->get( Route_Definer::ROUTES ) as $route ) {
			// Register any route hooks.
			$route->register();

			$patterns   = $route->get_patterns();
			$route_vars = $route->get_query_var_names();

			foreach ( $patterns as $pattern ) {
				$this->routes[ $pattern ] = $route;
			}

			$this->router_vars = array_merge( $this->router_vars, $route_vars );
		}

		$this->router_vars = array_unique( $this->router_vars );
	}

	/**
	 * Register REST API routes.
	 *
	 * @return void
	 */
	public function init_rest_routes() : void {
		foreach ( $this->container->get( Route_Definer::REST_ROUTES ) as $route ) {
			$route->register();
		}
	}

	/**
	 * Merges the WordPress Rewrite Rules with the CS Rules.
	 *
	 * Tied to the rewrite_rules_array hook.
	 *
	 * @param array $wp_rules The wp rules array.
	 * @return array          The modified rewrite rules array.
	 */
	public function load( $wp_rules = [] ) : array {
		$mt_trial_rules = $this->get_rules();
		$new_rules      = array_merge( $mt_trial_rules, $wp_rules );

		return $new_rules;
	}

	/**
	 * Looks up the custom route for the pattern matched. Returns false
	 * if not found.
	 *
	 * @param string $pattern The regex pattern to lookup.
	 * @return Route|false The route or false on failure.
	 */
	public function find_route( $pattern ) {
		if ( empty( $this->routes ) ) {
			$this->routes = $this->get_route_objects();
		}

		if ( empty( $this->routes[ $pattern ] ) ) {
			return false;
		}

		return $this->routes[ $pattern ];
	}

	/**
	 * Checks if the current route is a custom route and activates it if matched.
	 *
	 * Tied to parse_request hook.
	 *
	 * @param \WP $wp The global wp object.
	 * @return string|bool The matched route on success, false on failure.
	 */
	public function did_parse_request( $wp ) {
		$pattern       = $wp->matched_rule;
		$matched_route = $this->find_route( $pattern );

		// Bail early if no matched route.
		if ( empty( $matched_route ) ) {
			return false;
		}

		$this->matched_route = $matched_route;
		$this->matched_route->activate( $wp );

		return $matched_route;
	}

	/**
	 * Adds the custom query vars.
	 *
	 * Tied to query_vars hook
	 *
	 * @param array $query_vars WordPress query vars.
	 * @return array            Modified query vars.
	 */
	public function did_query_vars( $query_vars ) {
		if ( empty( $this->router_vars ) ) {
			return $query_vars;
		}

		return array_merge( $query_vars, $this->router_vars );
	}
}
