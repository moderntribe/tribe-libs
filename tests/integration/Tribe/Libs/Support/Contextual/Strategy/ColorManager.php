<?php declare(strict_types=1);

namespace Tribe\Libs\Support\Contextual\Strategy;

class ColorManager {

	/**
	 * @var \Tribe\Libs\Support\Contextual\Strategy\Color
	 */
	private $color;

	public function __construct( Color $color ) {
		$this->color = $color;
	}

	public function get_color(): string {
		return $this->color->get();
	}

}
