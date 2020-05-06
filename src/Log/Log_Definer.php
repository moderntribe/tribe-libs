<?php declare( strict_types=1 );

namespace Tribe\Libs\Log;

use DI;
use Monolog\Logger as MonoLogger;
use Monolog\Handler\StreamHandler;
use MHCG\Monolog\Handler\WPCLIHandler;
use Tribe\Libs\Container\Definer_Interface;

class Log_Definer implements Definer_Interface {

	public function define(): array {
		return [
			Psr\Log\LoggerInterface::class => DI\factory( function () {
				$path        = WP_CONTENT_DIR . '/square-one-' . date( 'Y-m-d' ) . '.log';
				$path        = apply_filters( 'tribe/log/path', $path );
				$handler     = new StreamHandler( $path, MonoLogger::DEBUG );
				$cli_handler = new WPCLIHandler( MonoLogger::DEBUG );

				$logger = new Logger( $handler );
				$logger->add_push_handler( $cli_handler );

				return $logger;
			} ),
		];
	}

}
