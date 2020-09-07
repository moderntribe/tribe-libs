<?php
declare( strict_types=1 );

namespace Tribe\Libs\Routes;

use Tribe\Libs\Container\Abstract_Subscriber;
use Tribe\Libs\Routes\Router;

class Route_Subscriber extends Abstract_Subscriber {
	public function register(): void {      
		add_action(
			'wp_loaded', 
			function ( ...$args ) {
				$this->container->get( Router::class )->flush_if_changed( ...$args );
			},
			999
		);

		add_action(
			'parse_request', 
			function ( ...$args ) {
				$this->container->get( Router::class )->did_parse_request( ...$args );
			}
		);

		add_filter(
			'query_vars', 
			function ( ...$args ) {
				return $this->container->get( Router::class )->did_query_vars( ...$args );
			}
        );

		add_filter(
			'rewrite_rules_array', 
			function ( ...$args ) {

				return $this->container->get( Router::class )->load( ...$args );
			},
			999
		);

		add_action(
			'rest_api_init', 
			function ( ...$args ) {
				$this->container->get( Router::class )->init_rest_routes( ...$args );
			}
		);
    }
}
