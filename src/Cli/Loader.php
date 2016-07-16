<?php
namespace Tribe\Libs\Cli;

class Loader {

	const DEFAULT_COMMAND = 's1';

	public static function load_commands() {
		\WP_CLI::add_command( self::command_prefix( 'generate' ), 'Tribe\Libs\Cli\Generate' );
	}

	/**
	 * @param string $name
	 *
	 * @return string
	 */
	private static function command_prefix( $name ) {
		$command = defined( 'TRIBE_WP_COMMAND' ) ? TRIBE_WP_COMMAND : self::DEFAULT_COMMAND;

		return sprintf( '%s %s', $command, $name );
	}

}