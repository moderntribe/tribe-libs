<?php

namespace Tribe\Libs\Queues\Tasks;

use Tribe\Libs\Queues\Contracts\Task;

class Noop implements Task {
	public function handle( array $args ) : bool {
		$success = rand( 0, 10 );
		if ( $success ) {
			\WP_CLI::line( 'Noop task ' . $args['noop'] .  ' processed' );
			return true;
		}

		\WP_CLI::line( 'Noop task failed, releasing ack' );
		return false;

	}
}