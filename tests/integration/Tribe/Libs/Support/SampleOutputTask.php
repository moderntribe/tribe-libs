<?php declare(strict_types=1);

namespace Tribe\Libs\Support;

use Tribe\Libs\Queues\Contracts\Task;

/**
 * A sample output task that outputs the object's ID.
 */
class SampleOutputTask implements Task {

	public function handle( array $args ): bool {
		print_r( spl_object_id( $this ) );

		return true;
	}

	public function get_object_id(): int {
		return spl_object_id( $this );
	}

}
