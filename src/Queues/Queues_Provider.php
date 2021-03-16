<?php

namespace Tribe\Libs\Queues;

use Pimple\Container;
use Tribe\Libs\Container\Service_Provider;
use Tribe\Libs\Queues\Contracts\Backend;
use Tribe\Libs\Queues\Backends\WP_Cache;
use Tribe\Libs\Queues\CLI\Add_Tasks;
use Tribe\Libs\Queues\CLI\Cleanup;
use Tribe\Libs\Queues\CLI\List_Queues;
use Tribe\Libs\Queues\CLI\Process;

class Queues_Provider extends Service_Provider {

	const WP_CACHE         = 'queues.backend.wp_cache';
	const DEFAULT_QUEUE    = 'queues.DefaultQueue';
	const COLLECTION       = 'queues.collection';
	const CRON             = 'queues.cron';
	const QUEUES_LIST      = 'queues.cli.list';
	const QUEUES_ADD_TABLE = 'queues.cli.add_table';
	const QUEUES_CLEANUP   = 'queues.cli.cleanup';
	const QUEUES_PROCESS   = 'queues.cli.process';
	const QUEUES_ADD_TASK  = 'queues.cli.add_tasks';


	public function register( Container $container ) {
		$this->backends( $container );
		$this->queues( $container );
		$this->cli( $container );
		$this->cron( $container );
	}

	/**
	 * Register available backends
	 *
	 * @param Container $container
	 *
	 */
	protected function backends( Container $container ) {
		$this->cache_backend( $container );
	}

	protected function cache_backend( Container $container ) {
		$container[ self::WP_CACHE ] = static function () {
			return new WP_Cache();
		};
	}

	/**
	 * Register available queues
	 *
	 * @param Container $container
	 *
	 */
	protected function queues( Container $container ) {
		$container[ self::DEFAULT_QUEUE ] = static function ( $container ) {
			/**
			 * Filter the backend supplied to the default queue.
			 */
			$backend = apply_filters( 'tribe/libs/queues/backend/default', $container[ self::WP_CACHE ] );
			if ( empty( $backend ) || ! $backend instanceof Backend ) {
				throw new \RuntimeException( 'Invalid backend provided for default queue' );
			}

			return new DefaultQueue( $backend );
		};

		$container[ self::COLLECTION ] = static function ( $container ) {
			$collection = new Queue_Collection();
			$collection->add( $container[ self::DEFAULT_QUEUE ] );

			return $collection;
		};
	}

	protected function cli( Container $container ) {
		$container[ self::QUEUES_LIST ] = static function ( $container ) {
			return new List_Queues( $container[ self::COLLECTION ] );
		};

		$container[ self::QUEUES_CLEANUP ] = static function ( $container ) {
			return new Cleanup( $container[ self::COLLECTION ] );
		};

		$container[ self::QUEUES_PROCESS ] = static function ( $container ) {
			return new Process( $container[ self::COLLECTION ] );
		};

		$container[ self::QUEUES_ADD_TASK ] = static function ( $container ) {
			return new Add_Tasks( $container[ self::COLLECTION ] );
		};

		add_action( 'init', static function () use ( $container ) {
			if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
				return;
			}

			$container[ self::QUEUES_LIST ]->register();
			$container[ self::QUEUES_CLEANUP ]->register();
			$container[ self::QUEUES_PROCESS ]->register();
			$container[ self::QUEUES_ADD_TASK ]->register();
		}, 0, 0 );
	}

	/**
	 * @param Container $container
	 */
	protected function cron( Container $container ) {
		$container[ self::CRON ] = static function ( $container ) {
			return new Cron();
		};

		if ( ! defined( 'DISABLE_WP_CRON' ) || false === DISABLE_WP_CRON ) {
			add_filter( 'cron_schedules', static function ( $schedules ) use ( $container ) {
				return $container[ self::CRON ]->add_interval( $schedules );
			}, 10, 1 );

			add_action( 'admin_init', static function () use ( $container ) {
				$container[ self::CRON ]->schedule_cron();
			}, 10, 0 );

			add_action( Cron::CRON_ACTION, static function () use ( $container ) {
				foreach ( $container[ self::COLLECTION ]->queues() as $queue ) {
					$container[ self::CRON ]->process_queues( $queue );
					$container[ self::CRON ]->cleanup( $queue );
				}
			}, 10, 0 );
		}
	}
}
