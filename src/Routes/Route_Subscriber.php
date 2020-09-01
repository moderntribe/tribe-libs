<?php
declare( strict_types=1 );

namespace Tribe\Libs\Routes;

use Tribe\Libs\Container\Abstract_Subscriber;

class Route_Subscriber extends Abstract_Subscriber {
	public function register(): void {
		add_action( 'init', function () {
            array_map( [ $this, 'register_route' ], $this->container->get( Route_Definer::ROUTES ) );
		}, 10, 0 );
    }
    
    public function register_route( Route $route ) {
        $route->register();
    }
}
