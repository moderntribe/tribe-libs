<?php declare(strict_types=1);

namespace Tribe\Libs\Support;

use Tribe\Libs\Cache\Cache;
use Tribe\Libs\Queues\Contracts\Task;

/**
 * A sample queue task used in automated tests.
 */
class SampleTask implements Task {

	public const CACHE_KEY = 'test_task_number';

	private $cache;

	public function __construct( Cache $cache ) {
		$this->cache = $cache;
	}

	public function handle( array $args ): bool {
		[ $number ] = $args;

		$this->cache->set( self::CACHE_KEY, (int) $number );

		return true;
	}

	public function get_number(): int {
		return $this->cache->get( self::CACHE_KEY ) ?: 0;
	}

}
