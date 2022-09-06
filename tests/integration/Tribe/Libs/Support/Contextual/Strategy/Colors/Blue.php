<?php declare(strict_types=1);

namespace Tribe\Libs\Support\Contextual\Strategy\Colors;

use Tribe\Libs\Support\Contextual\Strategy\Color;

final class Blue implements Color {

	public function get(): string {
		return 'blue';
	}

}
