<?php


namespace Tribe\Libs\Container;

use Pimple\Container;

class Container_Provider extends Service_Provider {
	const EXPORT_COMMAND = 'container.cli.export';

	/**
	 * @param Container $container
	 *
	 * @return void
	 */
	public function register( Container $container ) {
		$this->cli( $container );
	}

	/**
	 * @param Container $container
	 *
	 * @return void
	 */
	private function cli( Container $container ) {
		$container[ self::EXPORT_COMMAND ] = function ( Container $container ) {
			return new Export_Command( tribe_project(), $container );
		};

		add_action( 'init', function () use ( $container ) {
			if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
				return;
			}

			$container[ self::EXPORT_COMMAND ]->register();
		}, 0, 0 );
	}
}