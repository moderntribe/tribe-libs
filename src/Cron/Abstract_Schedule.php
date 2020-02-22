<?php

namespace Tribe\Project\Cron;

/**
 * Class Abstract_Schedule
 *
 * Extend this class to create a custom WordPress schedule to use with cron jobs.
 *
 * @package Tribe\Project\Cron
 */
abstract class Abstract_Schedule {

	/**
	 * The unique array key of your schedule, e.g. five_seconds
	 *
	 * @var string
	 */
	const KEY = '';

	/**
	 * The interval in seconds this schedule will repeat
	 *
	 * @var int
	 */
	const INTERVAL = 0;

	/**
	 * The nice name your schedule will show if ever displayed
	 *
	 * @var string
	 */
	protected $display;

	/**
	 * Abstract_Schedule constructor.
	 *
	 * @param  string  $display
	 */
	public function __construct( string $display ) {
		if ( static::KEY === '' ) {
			throw new \LogicException( 'A cron schedule requires an array key.' );
		}

		if ( static::INTERVAL < 1 ) {
			throw new \LogicException( 'A cron schedule requires an interval greater than 0 seconds.' );
		}

		$this->display = $display;
	}

	/**
	 * Returns the schedule formatted for the 'cron_schedules' filter.
	 *
	 * @return array
	 */
	public function get(): array {
		$schedules[ static::KEY ] = [
			'interval' => static::INTERVAL,
			'display'  => esc_html__( $this->display, 'tribe' ),
		];

		return (array) $schedules;
	}
}