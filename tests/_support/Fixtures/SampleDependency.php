<?php declare(strict_types=1);

namespace Tribe\Libs\Tests\Fixtures;

/**
 * A sample class to test the container.
 */
class SampleDependency {

	public function get_object_id(): int {
		return spl_object_id( $this );
	}

}
