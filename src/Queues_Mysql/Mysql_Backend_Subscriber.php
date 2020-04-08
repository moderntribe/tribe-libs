<?php
declare( strict_types=1 );

namespace Tribe\Libs\Queues_Mysql;

use Psr\Container\ContainerInterface;
use Tribe\Libs\Container\Subscriber_Interface;
use Tribe\Libs\Queues_Mysql\Backends\MySQL;
use Tribe\Libs\Queues_Mysql\CLI\MySQL_Table;

class Mysql_Backend_Subscriber implements Subscriber_Interface {
	public function register( ContainerInterface $container ): void {
		$this->backend( $container );
		$this->cli( $container );
	}

	/**
	 * Register the backend
	 *
	 * @param ContainerInterface $container
	 *
	 */
	protected function backend( ContainerInterface $container ) {
		add_action( 'tribe/project/queues/mysql/init_table', function () use ( $container ) {
			$container->get( MySQL::class )->initialize_table();
		}, 10, 0 );

		add_action( 'admin_init', function () {
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				do_action( 'tribe/project/queues/mysql/init_table' );
			}
		}, 0, 0 );
	}

	protected function cli( ContainerInterface $container ) {
		add_action( 'init', function () use ( $container ) {
			if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
				return;
			}
			$container->get( MySQL_Table::class )->register();
		}, 0, 0 );
	}
}
