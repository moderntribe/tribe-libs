<?php

namespace Tribe\Libs\CLI;

use WP_CLI;

abstract class Command extends \WP_CLI_Command implements Command_Interface {

	public function register() {
		WP_CLI::add_command( 's1 ' . $this->command(), [ $this, 'run_command' ], [
			'shortdesc' => $this->description(),
			'synopsis'  => $this->arguments(),
		] );
	}

	abstract protected function command();
	abstract protected function description();
	abstract protected function arguments();

}
