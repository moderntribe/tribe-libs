<?php

namespace Tribe\Project\Cron;

/**
 * Class Cron
 *
 * Extend this class to create custom WordPress cron jobs.
 *
 * @package Tribe\Project\Cron
 */
abstract class Abstract_Cron {

	/**
	 * The hook name to schedule the job on
	 *
	 * @var string
	 */
	protected $hook;

	/**
	 * A valid WordPress schedule, e.g. hourly, twicedaily, daily
	 *
	 * @see wp_get_schedules()
	 *
	 * @var string How often the cron job runs in seconds
	 */
	protected $recurrence;

	/**
	 * The arguments to pass to the hook when cron job runs
	 *
	 * @var array
	 */
	protected $args;

	/**
	 * Abstract_Cron constructor.
	 *
	 * @param  string  $hook
	 * @param  string  $recurrence  see wp_get_schedules(), e.g. hourly, twicedaily, daily
	 * @param  array  $args
	 */
	public function __construct( string $hook, string $recurrence, array $args = [] ) {
		$this->hook       = $hook;
		$this->recurrence = $recurrence;
		$this->args       = $args;
	}

	/**
	 * Registers the cron job
	 *
	 * @param  Abstract_Cron  $instance
	 */
	public function register( Abstract_Cron $instance ) {
		add_action( $this->hook, [ $instance, 'run' ], 10, 1 );
	}

	/**
	 * Enables the cron job
	 */
	public function enable() {
		if ( ! wp_next_scheduled( $this->hook ) ) {
			wp_schedule_event( time(), $this->recurrence, $this->hook, $this->args );
		}
	}

	/**
	 * Disables the cron job
	 */
	public function disable() {
		wp_clear_scheduled_hook( $this->hook );
	}

	/**
	 * Executes when the cron job runs
	 *
	 * @param  array  $args
	 *
	 * @return mixed
	 */
	abstract public function run( array $args = [] );
}
