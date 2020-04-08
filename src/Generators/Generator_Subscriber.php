<?php
declare( strict_types=1 );

namespace Tribe\Libs\Generators;

use Psr\Container\ContainerInterface;
use Tribe\Libs\Container\Subscriber_Interface;

class Generator_Subscriber implements Subscriber_Interface {
	public function register( ContainerInterface $container ): void {
		add_action( 'init', function () use ( $container ) {
			if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
				return;
			}
			foreach ( $container->get( Generator_Definer::COMMANDS ) as $command ) {
				$command->register();
			}
		}, 0, 0 );
	}
}
