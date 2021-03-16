<?php

namespace Tribe\Libs\Queues;

use Exception;
use Tribe\Libs\Queues\Contracts\Task;
use Tribe\Libs\Queues\Contracts\Queue;

/**
 * Process queues via the WordPress cron
 *
 * @package Tribe\Libs\Queues
 */
class Cron {

	const CRON_ACTION = 'tribe_queue_cron';
	const FREQUENCY   = 'tribe_queue_frequency';

	private $frequency_in_seconds;
	private $timelimit_in_seconds;

	/**
	 * Cron constructor.
	 *
	 * @param  int  $frequency
	 * @param  int  $timelimit
	 */
	public function __construct( int $frequency = 60, int $timelimit = 15 ) {
		$this->frequency_in_seconds = $frequency;
		$this->timelimit_in_seconds = $timelimit;
	}

	/**
	 * Process a Queue's tasks.
	 *
	 * @param  \Tribe\Libs\Queues\Contracts\Queue  $queue
	 *
	 * @action self::CRON_ACTION
	 *
	 * @return void
	 */
	public function process_queues( Queue $queue ) {
		$end_time = time() + $this->timelimit_in_seconds;

		while ( time() < $end_time ) {

			if ( ! $queue->count() ) {
				return;
			}

			try {
				$job = $queue->reserve();
			} catch ( Exception $e ) {
				return;
			}

			$task_class = $job->get_task_handler();

			if ( ! class_exists( $task_class ) ) {
				$queue->nack( $job->get_job_id() );

				return;
			}

			/** @var Task $task */
			$task = new $task_class();

			if ( $task->handle( $job->get_args() ) ) {
				// Acknowledge.
				$queue->ack( $job->get_job_id() );
			} else {
				$queue->nack( $job->get_job_id() );
			}
		}
	}

	/**
	 * Schedule a cron job.
	 *
	 * @action admin_init
	 *
	 * @return void
	 */
	public function schedule_cron() {
		if ( ! wp_next_scheduled( self::CRON_ACTION ) ) {
			wp_schedule_event( time(), self::FREQUENCY, self::CRON_ACTION );
		}
	}

	/**
	 * Filter existing WordPress cron schedules.
	 *
	 * @param  array  $cron_schedules
	 *
	 * @return array
	 *
	 * @filter cron_schedules
	 */
	public function add_interval( array $cron_schedules = [] ): array {
		$cron_schedules[ self::FREQUENCY ] = [
			'interval' => $this->frequency_in_seconds,
			'display'  => __( 'Queue Cron Schedule', 'tribe' ),
		];

		return $cron_schedules;
	}

	/**
	 * Remove completed and timed out tasks from the queue after a certain ttl.
	 *
	 * @param  \Tribe\Libs\Queues\Contracts\Queue  $queue
	 *
	 * @return void
	 */
	public function cleanup( Queue $queue ) {
		$queue->cleanup();
	}

}
