<?php
declare( strict_types=1 );

namespace Tribe\Libs\Queues;

use Tribe\Libs\Container\Abstract_Subscriber;

class Queues_Subscriber extends Abstract_Subscriber {
	public function register(): void {
		$this->cron();
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
