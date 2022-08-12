<?php declare(strict_types=1);

namespace Tribe\Libs\Routes;

use DI;
use Tribe\Libs\Container\Definer_Interface;

/**
 * Defines any routes to be registered for the project.
 *
 * @package Tribe\Project\Routes
 */
class Route_Definer implements Definer_Interface {

	/**
	 * The Route DI group to use.
	 */
	public const ROUTES = 'libs.routes';

	/**
	 * The REST DI group to use.
	 */
	public const REST_ROUTES = 'libs.rest_routes';

	/**
	 * @return mixed[]
	 */
	public function define(): array {
		return [
			/**
			 * The array of routes that will be registered.
			 * Add more in other Definers using \Tribe\Libs\Routes\Route_Definer::ROUTES => DI\add( [ ... ] ).
			 *
			 * ROUTES should extend \Tribe\Libs\Routes\Abstract_Route
			 */
			self::ROUTES      => DI\add( [] ),

			/**
			 * The array of REST routes that will be registered.
			 * Add more in other Definers using \Tribe\Libs\Routes\Route_Definer::REST_ROUTES => DI\add( [ ... ] ).
			 *
			 * REST ROUTES should extend \Tribe\Libs\Routes\Abstract_REST_Route
			 */
			self::REST_ROUTES => DI\add( [] ),
		];
	}

}
