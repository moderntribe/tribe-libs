<?php
declare( strict_types=1 );

namespace Tribe\Libs\CLI;

use DI;
use Tribe\Libs\Container\Definer_Interface;

class CLI_Definer implements Definer_Interface {
	public const COMMANDS = 'cli.commands';

	public function define(): array {
		return [
			/**
			 * Use \DI\add() from other definers to add commands. Any commands
			 * thus added will be registered with WP_CLI.
			 */
			self::COMMANDS => DI\add( [] ),
		];
	}
}
