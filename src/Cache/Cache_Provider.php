<?php


namespace Tribe\Libs\Cache;


use Pimple\Container;
use Tribe\Libs\Container\Service_Provider;

class Cache_Provider extends Service_Provider {
	const CACHE    = 'cache.cache';
	const LISTENER = 'cache.listener';
	const PURGER   = 'cache.purger';

	public function register( Container $container ) {
		$this->cache( $container );
		$this->listen( $container );
		$this->purge( $container );
	}

	protected function cache( Container $container ) {
		$container[ self::CACHE ] = function ( Container $container ) {
			return new Cache();
		};
	}

	protected function listen( Container $container ) {
		$container[ self::LISTENER ] = function ( Container $container ) {
			return new Listener( $container[ self::CACHE ] );
		};
	}

	protected function purge( Container $container ) {
		$container[ self::PURGER ] = function ( Container $container ) {
			return new Purger( 'manage_options' );
		};

		add_action( 'init', function () use ( $container ) {
			$container[ self::PURGER ]->maybe_purge_cache();
		}, 9, 0 );

		add_action( 'admin_bar_menu', function ( $admin_bar ) use ( $container ) {
			$container[ self::PURGER ]->add_admin_bar_button( $admin_bar );
		}, 100, 1 );
	}

}