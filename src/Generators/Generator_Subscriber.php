<?php
declare( strict_types=1 );

namespace Tribe\Libs\Generators;

use Tribe\Libs\Container\Abstract_Subscriber;

class Generator_Subscriber extends Abstract_Subscriber {
	public function register(): void {
		add_action( 'init', function () {
			if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
				return;
			}
			foreach ( $this->container->get( Generator_Definer::COMMANDS ) as $command ) {
				$command->register();
			}
		}, 0, 0 );
	}
}
