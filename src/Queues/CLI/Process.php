<?php

namespace Tribe\Libs\Queues\CLI;

use DI;
use Exception;
use Throwable;
use Tribe\Libs\CLI\Command;
use Tribe\Libs\Queues\Contracts\Task;
use Tribe\Libs\Queues\Queue_Collection;
use WP_CLI;

use function WP_CLI\Utils\get_flag_value;

class Process extends Command {

	public const ARG_QUEUE     = 'queue';
	public const ARG_TIMELIMIT = 'timelimit';

	public const DEFAULT_TIMELIMIT = 300;

	/**
	 * @var Queue_Collection
	 */
	protected $queues;

	/**
	 * @var \DI\FactoryInterface
	 */
	protected $container;

	/**
	 * @var int How long the process should run, in seconds. If 0,
	 *          the process will run until it meets a fatal error
	 *          (e.g., out of memory).
	 */
	private $timelimit;

	public function __construct( Queue_Collection $queue_collection, DI\FactoryInterface $container ) {
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
				'name'        => self::ARG_QUEUE,
				'optional'    => false,
				'description' => __( 'The name of the Queue.', 'tribe' ),
			],
			[
				'type'        => 'assoc',
				'name'        => self::ARG_TIMELIMIT,
				'optional'    => true,
				'description' => __( 'The max process execution in seconds.', 'tribe' ),
				'default'     => self::DEFAULT_TIMELIMIT,
			],
		];
	}

	public function run_command( $args, $assoc_args ) {
		if ( ! isset( $args[0] ) ) {
			WP_CLI::error( __( 'You must specify which queue you wish to process.', 'tribe' ) );
		}

		[ $queue_name ]  = $args;
		$this->timelimit = (int) get_flag_value( $assoc_args, self::ARG_TIMELIMIT, self::DEFAULT_TIMELIMIT );

		if ( ! array_key_exists( $queue_name, $this->queues->queues() ) ) {
			WP_CLI::error( __( "That queue name doesn't appear to be valid.", 'tribe' ) );
		}

		try {
			$queue = $this->queues->get( $queue_name );
		} catch ( Exception $e ) {
			WP_CLI::error( $e->getMessage() );
		}

		$endtime = time() + $this->timelimit;

		$runtime_message = sprintf( 'The queue will run for %d seconds', $this->timelimit );

		if ( $this->timelimit === 0 ) {
			$runtime_message = 'Timelimit set to 0. The queue will run indefinitely or until memory is exhausted';
		}

		WP_CLI::log(
			sprintf(
				__( 'Processing queue: %s. %s. Run with the --debug option to see additional details.', 'tribe' ),
				$queue_name,
				$runtime_message
			)
		);

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
				$task = $this->container->make( $task_class );
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
