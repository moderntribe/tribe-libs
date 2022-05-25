<?php declare(strict_types=1);

namespace Tribe\Libs\Pipeline\Contracts;

use Closure;

interface Pipeline {

	/**
	 * Set the traveler object being sent on the pipeline.
	 *
	 * @param  mixed  $traveler
	 *
	 * @return $this
	 */
	public function send( $traveler ): Pipeline;

	/**
	 * Set the stops of the pipeline.
	 *
	 * @param  array|mixed  $stops
	 *
	 * @return $this
	 */
	public function through( $stops ): Pipeline;

	/**
	 * Set the method to call on the stops.
	 *
	 * @param  string  $method
	 *
	 * @return $this
	 */
	public function via( string $method ): Pipeline;

	/**
	 * Run the pipeline with a final destination callback.
	 *
	 * @param  \Closure  $destination
	 *
	 * @return mixed
	 */
	public function then( Closure $destination );

	/**
	 * Run the pipeline and return the result.
	 *
	 * @return mixed
	 */
	public function thenReturn();

}
