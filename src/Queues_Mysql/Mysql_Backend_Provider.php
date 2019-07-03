<?php


namespace Tribe\Libs\Queues_Mysql;

use Pimple\Container;
use Tribe\Libs\Container\Service_Provider;
use Tribe\Libs\Queues_Mysql\Backends\MySQL;
use Tribe\Libs\Queues_Mysql\CLI\MySQL_Table;

class Mysql_Backend_Provider extends Service_Provider {
	const BACKEND       = 'queues.mysql.backend';
	const CLI_ADD_TABLE = 'queues.mysql.cli.add_table';


	public function register( Container $container ) {
		$this->backend( $container );
		$this->cli( $container );
	}

	/**
	 * Register the backend
	 *
	 * @param Container $container
	 *
	 */
	protected function backend( Container $container ) {
		$container[ self::BACKEND ] = function () {
			return new MySQL();
		};

		add_action( 'tribe/project/queues/mysql/init_table', function () use ( $container ) {
			$container[ self::BACKEND ]->initialize_table();
		}, 10, 0 );

		add_action( 'admin_init', function () {
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				do_action( 'tribe/project/queues/mysql/init_table' );
			}
		}, 0, 0 );

		add_filter( 'tribe/libs/queues/backend/default', function( $backend ) use ( $container ) {
			return $container[ self::BACKEND ];
		}, 10, 1 );
	}

	protected function cli( Container $container ) {
		$container[ self::CLI_ADD_TABLE ] = function ( $container ) {
			return new MySQL_Table( $container[ self::BACKEND ] );
		};

		add_action( 'init', function () use ( $container ) {
			if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
				return;
			}
			$container[ self::CLI_ADD_TABLE ]->register();
		}, 0, 0 );
	}

}