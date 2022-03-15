<?php declare(strict_types=1);

namespace Tribe\Libs\Support\Contextual\Strategy;

interface Color {

	/**
	 * Get the color of this strategy.
	 *
	 * @return string
	 */
	public function get(): string;

}
