<?php declare(strict_types=1);

namespace Tribe\Libs\Queues\CLI;

use Exception;
use Throwable;
use Tribe\Libs\CLI\Command;
use Tribe\Libs\Container\StatefulContainer;
use Tribe\Libs\Queues\Contracts\Task;
use Tribe\Libs\Queues\Queue_Collection;
use WP_CLI;

class Process extends Command {

	/**
	 * @var Queue_Collection
	 */
	protected $queues;

	/**
	 * @var \Tribe\Libs\Container\StatefulContainer
	 */
	protected $container;

	/**
	 * @var int How long the process should run, in seconds. If 0,
	 *          the process will run until it meets a fatal error
	 *          (e.g., out of memory).
	 */
	private $timelimit = 300;

	public function __construct( Queue_Collection $queue_collection, StatefulContainer $container ) {
		$this->queues    = $queue_collection;
		$this->container = $container;
		parent::__construct();
	}

	public function command() {
		return 'queues process';
	}

	public function description() {
		return __( 'Process the queue for the provided queue name.', 'tribe' );
	}

	public function arguments() {
		return [
			[
				'type'        => 'positional',
				'name'        => 'queue',
				'optional'    => false,
				'description' => __( 'The name of the Queue.', 'tribe' ),
			],
		];
	}

	public function run_command( $args, $assoc_args ) {
		if ( ! isset( $args[0] ) ) {
			WP_CLI::error( __( 'You must specify which queue you wish to process.', 'tribe' ) );
		}

		$queue_name = $args[0];

		if ( ! array_key_exists( $queue_name, $this->queues->queues() ) ) {
			WP_CLI::error( __( "That queue name doesn't appear to be valid.", 'tribe' ) );
		}

		try {
			$queue = $this->queues->get( $queue_name );
		} catch ( Exception $e ) {
			WP_CLI::error( $e->getMessage() );
		}

		$endtime = time() + $this->timelimit;

		// Run forever.
		while ( $this->timelimit === 0 || time() < $endtime ) {

			// If the queue is empty, sleep on it and then clear it again.
			if ( ! $queue->count() ) {
				WP_CLI::debug( __( 'Queue is empty. Sleeping...', 'tribe' ) );
				sleep( 1 );
				continue;
			}

			try {
				$job = $queue->reserve();
			} catch ( Exception $e ) {
				WP_CLI::debug( __( 'Unable to reserve task from queue. Sleeping...', 'tribe' ) );
				sleep( 1 );
				continue;
			}

			$task_class = $job->get_task_handler();

			try {
				/** @var Task $task */
				$task = $this->container->refresh()->make( $task_class );
			} catch ( Throwable $e ) {
				WP_CLI::debug( sprintf( __( 'Unable to create task instance for class "%s". Error: %s', 'tribe' ), $task_class, $e->getMessage() ) );
				$queue->nack( $job->get_job_id() );

				return;
			}

			if ( $task->handle( $job->get_args() ) ) {
				// Acknowledge.
				WP_CLI::debug( sprintf( 'ACK: %s (%s)', $job->get_job_id(), $task_class ) );
				$queue->ack( $job->get_job_id() );
			} else {
				WP_CLI::debug( sprintf( 'NACK: %s (%s)', $job->get_job_id(), $task_class ) );
				$queue->nack( $job->get_job_id() );
			}
		}
	}
}
