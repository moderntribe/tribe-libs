<?php
declare( strict_types=1 );

namespace Tribe\Libs\Queues_Mysql;

use Tribe\Libs\Container\Abstract_Subscriber;
use Tribe\Libs\Queues_Mysql\Backends\MySQL;
use Tribe\Libs\Queues_Mysql\CLI\MySQL_Table;

class Mysql_Backend_Subscriber extends Abstract_Subscriber {
	public function register(): void {
		$this->backend();
		$this->cli();
	}

	/**
	 * Register the backend
	 */
	protected function backend() {
		add_action( 'tribe/project/queues/mysql/init_table', function () {
			$this->container->get( MySQL::class )->initialize_table();
		}, 10, 0 );

		add_action( 'admin_init', function () {
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				do_action( 'tribe/project/queues/mysql/init_table' );
			}
		}, 0, 0 );
	}

	protected function cli() {
		add_action( 'init', function () {
			if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
				return;
			}
			$this->container->get( MySQL_Table::class )->register();
		}, 0, 0 );
	}
}
