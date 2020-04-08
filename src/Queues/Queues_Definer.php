<?php
declare( strict_types=1 );

namespace Tribe\Libs\Queues;

use DI;
use Tribe\Libs\Container\Definer_Interface;
use Tribe\Libs\Queues\Backends\WP_Cache;
use Tribe\Libs\Queues\Contracts\Backend;
use Tribe\Libs\Queues\Contracts\Queue;

class Queues_Definer implements Definer_Interface {
	public function define(): array {
		return [
			Backend::class          => DI\create( WP_Cache::class ),
			Queue::class            => DI\autowire( DefaultQueue::class ),
			Queue_Collection::class => DI\create()
				->method( 'add', DI\get( Queue::class ) ),
		];
	}
}
