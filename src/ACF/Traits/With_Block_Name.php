<?php declare(strict_types=1);

namespace Tribe\Libs\ACF\Traits;

/**
 * Trait With_Block_Name
 *
 * @package Tribe\Libs\ACF\Traits
 */
trait With_Block_Name {

	/**
	 * Get a SquareOne block name from an ACF Block data array.
	 *
	 * @param  array  $block The ACF Block Data.
	 *
	 * @return string
	 */
	protected function get_block_name( array $block ): string {
		return str_replace( 'acf/', '', $block['name'] );
	}
}
