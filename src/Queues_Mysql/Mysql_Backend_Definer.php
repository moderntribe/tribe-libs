<?php
declare( strict_types=1 );

namespace Tribe\Libs\Queues_Mysql;

use DI;
use Tribe\Libs\Container\Definer_Interface;
use Tribe\Libs\Queues\Contracts\Backend;
use Tribe\Libs\Queues_Mysql\Backends\MySQL;

class Mysql_Backend_Definer implements Definer_Interface {
	public function define(): array {
		return [
			Backend::class => DI\get( MySQL::class ),
		];
	}
}
