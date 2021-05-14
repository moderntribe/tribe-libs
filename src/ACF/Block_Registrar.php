<?php declare(strict_types=1);

namespace Tribe\Libs\ACF;

class Block_Registrar {
	/**
	 * @param Block_Config $block_config
	 */
	public function register( Block_Config $block_config ) {
		$this->register_fields( $block_config );
		$this->register_block( $block_config );
	}

	/**
	 * @param Block_Config $block_config
	 */
	protected function register_block( Block_Config $block_config ) {
		acf_register_block_type( $block_config->get_block()->get_attributes() );
	}

	/**
	 * @param  Block_Config  $block_config
	 */
	protected function register_fields( Block_Config $block_config ) {
		$group = $block_config->get_field_group();

		/**
		 * Allow developers to modify a block's fields before being registered.
		 *
		 * @param \Tribe\Libs\ACF\Field[] $fields An array of Field instances.
		 * @param \Tribe\Libs\ACF\Block_Config $block The block's instance.
		 */
		$fields = (array) apply_filters( 'tribe/block/register/fields', $block_config->get_fields(), $block_config );

		foreach ( $fields as $field ) {
			$group->add_field( $field );
		}

		acf_add_local_field_group( $group->get_attributes() );
	}
}
