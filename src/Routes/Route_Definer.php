<?php
declare( strict_types=1 );

namespace Tribe\Libs\Routes;

use DI;
use Tribe\Libs\Container\Definer_Interface;

class Route_Definer implements Definer_Interface {
    /**
     * The DI group to use.
     *
     * @var string
     */
	public const ROUTES = 'libs.routes.groups';

    /**
     * Defines routes.
     *
     * @return array
     */
	public function define(): array {
		return [
			/**
			 * The array of routes that will be registered with the Route Repository.
			 * Add more in other Definers using Route_Definer::ROUTES => DI\add( [ ... ] ).
			 *
			 * ROUTES should extend \Tribe\Libs\Routes\Route
			 */
            self::ROUTES => DI\add( [] ),

			Route_Repository::class => DI\create()
				->constructor( DI\get( self::ROUTES ) ),

			/**
			 * The array of routes that will be registered with the Route Repository.
			 * Add more in other Definers using Route_Definer::ROUTES => DI\add( [ ... ] ).
			 *
			 * ROUTES should extend \Tribe\Libs\Routes\Rest_Route
			 */
			self::REST_ROUTES => DI\add( [] ),

			Route_Repository::class => DI\create()
				->constructor( DI\get( self::REST_ROUTES ) ),
		];
	}
}
