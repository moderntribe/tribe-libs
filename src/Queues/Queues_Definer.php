<?php declare(strict_types=1);

namespace Tribe\Libs\Queues;

use DI;
use Tribe\Libs\CLI\CLI_Definer;
use Tribe\Libs\Container\Container;
use Tribe\Libs\Container\Definer_Interface;
use Tribe\Libs\Container\StatefulContainer;
use Tribe\Libs\Queues\Backends\WP_Cache;
use Tribe\Libs\Queues\CLI\Add_Tasks;
use Tribe\Libs\Queues\CLI\Cleanup;
use Tribe\Libs\Queues\CLI\List_Queues;
use Tribe\Libs\Queues\CLI\Process;
use Tribe\Libs\Queues\Contracts\Backend;
use Tribe\Libs\Queues\Contracts\Queue;

class Queues_Definer implements Definer_Interface {

	public function define(): array {
		return [
			StatefulContainer::class => DI\autowire( Container::class ),
			Backend::class           => DI\create( WP_Cache::class ),
			Queue::class             => DI\autowire( DefaultQueue::class ),
			Queue_Collection::class  => DI\create()
				->method( 'add', DI\get( Queue::class ) ),

			/**
			 * Add commands for the CLI subscriber to register
			 */
			CLI_Definer::COMMANDS    => DI\add( [
				DI\get( List_Queues::class ),
				DI\get( Add_Tasks::class ),
				DI\get( Cleanup::class ),
				DI\get( Process::class ),
			] ),
		];
	}

}
