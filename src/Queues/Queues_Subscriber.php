<?php
declare( strict_types=1 );

namespace Tribe\Libs\Queues;

use Psr\Container\ContainerInterface;
use Tribe\Libs\Container\Subscriber_Interface;
use Tribe\Libs\Queues\CLI\Add_Tasks;
use Tribe\Libs\Queues\CLI\Cleanup;
use Tribe\Libs\Queues\CLI\List_Queues;
use Tribe\Libs\Queues\CLI\Process;
use Tribe\Libs\Queues\Contracts\Backend;

class Queues_Subscriber implements Subscriber_Interface {
	public function register( ContainerInterface $container ): void {
		$this->cli( $container );
		$this->cron( $container );
	}

	protected function cli( ContainerInterface $container ): void {
		add_action( 'init', function () use ( $container ) {
			if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
				return;
			}

			$container->get( List_Queues::class )->register();
			$container->get( Cleanup::class )->register();
			$container->get( Process::class )->register();
			$container->get( Add_Tasks::class )->register();
		}, 0, 0 );
	}

	protected function cron( ContainerInterface $container ): void {
		if ( ! defined( 'DISABLE_WP_CRON' ) || false === DISABLE_WP_CRON ) {
			add_filter( 'cron_schedules', function ( $schedules ) use ( $container ) {
				$container->get( Cron::class )->add_interval( $schedules );
			}, 10, 1 );

			add_action( 'admin_init', function () use ( $container ) {
				$container->get( Cron::class )->schedule_cron();
			}, 10, 0 );

			add_action( Cron::CRON_ACTION, function () use ( $container ) {
				foreach ( $container->get( Queue_Collection::class )->queues() as $queue ) {
					$container->get( Cron::class )->process_queues( $queue );
				}
			}, 10, 0 );
		}
	}
}
