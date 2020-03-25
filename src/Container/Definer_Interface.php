<?php
declare( strict_types=1 );

namespace Tribe\Libs\Container;

use DI;

interface Definer_Interface {
	public function define(): array;
}
