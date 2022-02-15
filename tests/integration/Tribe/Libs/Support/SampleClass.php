<?php declare(strict_types=1);

namespace Tribe\Libs\Support;

/**
 * A sample class to test the container.
 */
class SampleClass {

	private $dep;

	public function __construct( SampleDependency $dep ) {
		$this->dep = $dep;
	}

	public function get_object_id(): int {
		return spl_object_id( $this );
	}

	public function get_sub_object_id(): int {
		return $this->dep->get_object_id();
	}

}
