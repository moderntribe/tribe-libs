<?php
declare( strict_types=1 );

namespace Tribe\Libs\Queues;

use Tribe\Libs\Container\Abstract_Subscriber;
use Tribe\Libs\Queues\CLI\Add_Tasks;
use Tribe\Libs\Queues\CLI\Cleanup;
use Tribe\Libs\Queues\CLI\List_Queues;
use Tribe\Libs\Queues\CLI\Process;
use Tribe\Libs\Queues\Contracts\Backend;

class Queues_Subscriber extends Abstract_Subscriber {
	public function register(): void {
		$this->cli();
		$this->cron();
	}

	protected function cli(): void {
		add_action( 'init', function () {
			if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
				return;
			}

			$this->container->get( List_Queues::class )->register();
			$this->container->get( Cleanup::class )->register();
			$this->container->get( Process::class )->register();
			$this->container->get( Add_Tasks::class )->register();
		}, 0, 0 );
	}

	protected function cron(): void {
		if ( ! defined( 'DISABLE_WP_CRON' ) || false === DISABLE_WP_CRON ) {
			add_filter( 'cron_schedules', function ( $schedules ) {
				$this->container->get( Cron::class )->add_interval( $schedules );
			}, 10, 1 );

			add_action( 'admin_init', function () {
				$this->container->get( Cron::class )->schedule_cron();
			}, 10, 0 );

			add_action( Cron::CRON_ACTION, function () {
				foreach ( $this->container->get( Queue_Collection::class )->queues() as $queue ) {
					$this->container->get( Cron::class )->process_queues( $queue );
				}
			}, 10, 0 );
		}
	}
}
