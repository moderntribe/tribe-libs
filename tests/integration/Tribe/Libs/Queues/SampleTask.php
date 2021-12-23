<?php declare(strict_types=1);

namespace Tribe\Libs\Queues;

use Tribe\Libs\Cache\Cache;
use Tribe\Libs\Queues\Contracts\Task;

/**
 * A sample queue task used in automated tests.
 */
class SampleTask implements Task {

	private $cache;

	public function __construct( Cache $cache ) {
		$this->cache = $cache;
	}

	public const CACHE_KEY = 'test_task_number';

	public function handle( array $args ): bool {
		[ $number ] = $args;

		$this->cache->set( self::CACHE_KEY, (int) $number );

		return true;
	}

	public function get_number(): int {
		return $this->cache->get( self::CACHE_KEY ) ?: 0;
	}

}
