<?php

namespace Tribe\Libs\Log;

use Pimple\Container;
use Monolog\Handler\StreamHandler;
use Monolog\Logger as MonoLogger;
use MHCG\Monolog\Handler\WPCLIHandler;
use Tribe\Libs\Container\Service_Provider;

/**
 * Log Service Provider
 *
 * @package Tribe\Libs\Log
 */
class Log_Provider extends Service_Provider {
	const HANDLER     = 'log.handler';
	const CLI_HANDLER = 'log.cli_handler';
	const LOGGER      = 'log.logger';
	const LOG_ACTIONS = 'log.log_actions';

	public function register( Container $container ) {
		$this->log( $container );
	}

	protected function log( Container $container ) {
		$container[ self::HANDLER ] = static function () {
			$path = WP_CONTENT_DIR . '/square-one-' . date( 'Y-m-d' ) . '.log';
			$path = apply_filters( 'tribe/log/path', $path );

			return new StreamHandler( $path, MonoLogger::DEBUG );
		};

		$container[ self::CLI_HANDLER ] = static function () {
			return new WPCLIHandler( MonoLogger::DEBUG );
		};

		$container[ self::LOGGER ] = static function ( Container $container ) {
			return new Logger( $container[ self::HANDLER ] );
		};

		$container[ self::LOGGER ]->add_push_handler( $container[ self::CLI_HANDLER ] );

		$container[ self::LOG_ACTIONS ] = static function ( Container $container ) {
			return new Log_Actions( $container[ self::LOGGER ] );
		};

		add_action( 'init', static function () use ( $container ) {

			// Set logger action hooks
			$container[ self::LOG_ACTIONS ]->init();

		}, 0, 0 );
	}


}