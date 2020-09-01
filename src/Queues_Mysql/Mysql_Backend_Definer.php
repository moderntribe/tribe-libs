<?php
declare( strict_types=1 );

namespace Tribe\Libs\Queues_Mysql;

use DI;
use Tribe\Libs\CLI\CLI_Definer;
use Tribe\Libs\Container\Definer_Interface;
use Tribe\Libs\Queues\Contracts\Backend;
use Tribe\Libs\Queues_Mysql\Backends\MySQL;
use Tribe\Libs\Queues_Mysql\CLI\MySQL_Table;

class Mysql_Backend_Definer implements Definer_Interface {
	public function define(): array {
		return [
			Backend::class        => DI\get( MySQL::class ),

			/**
			 * Add commands for the CLI subscriber to register
			 */
			CLI_Definer::COMMANDS => DI\add( [
				DI\get( MySQL_Table::class ),
			] ),
		];
	}
}
