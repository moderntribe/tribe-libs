<?php

namespace Tribe\Libs\Queues\Tasks;

use Tribe\Libs\Queues\Contracts\Task;

class Action implements Task {

	public function handle( array $args ) : bool {
		$action = $args['hook'];
		unset( $args['hook'] );
		try {
			do_action_ref_array( $action, $args );
			return true;
		} catch ( \Exception $e ) {
			return false;
		}
	}
}
