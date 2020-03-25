<?php
declare( strict_types=1 );

namespace Tribe\Libs\Cache;

use Psr\Container\ContainerInterface;
use Tribe\Libs\Container\Subscriber_Interface;

class Cache_Subscriber implements Subscriber_Interface {
	public function register( ContainerInterface $container ): void {
		$this->purge( $container );
	}

	protected function purge( ContainerInterface $container ): void {
		add_action( 'init', function () use ( $container ) {
			$container->get( Purger::class )->maybe_purge_cache();
		}, 9, 0 );

		add_action( 'admin_bar_menu', function ( $admin_bar ) use ( $container ) {
			$container->get( Purger::class )->add_admin_bar_button( $admin_bar );
		}, 100, 1 );
	}

}
