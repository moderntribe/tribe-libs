<?php

namespace Tribe\Project\Cron;

class Schedule_Collection {

	/** @var Abstract_Schedule[]  */
	private $schedules = [];

	/**
	 * Schedule_Collection constructor.
	 *
	 * @param  Abstract_Schedule  ...$schedules
	 */
	public function __construct( Abstract_Schedule ...$schedules ) {
		foreach( $schedules as $schedule ) {
			$this->schedules[ $schedule::KEY ] = $schedule;
		}
	}

	/**
	 * Get all the schedules in the collection
	 *
	 * @return array
	 */
	public function schedules(): array {
		return $this->schedules;
	}

	/**
	 * Get a specific schedule by key
	 *
	 * @param  string  $key
	 *
	 * @return Abstract_Schedule|null
	 */
	public function get( string $key ) {
		if ( isset( $this->schedules[ $key ] ) ) {
			return $this->schedules[ $key ];
		}

		return null;
	}
}