<?php
declare( strict_types=1 );

namespace Tribe\Libs\ACF;

abstract class Block_Config {
	public const NAME = '';

	/**
	 * @var array
	 */
	protected $items = [];

	/**
	 * @var Block
	 */
	protected $block;

	abstract public function add_block();

	protected function add_fields() {
		//overwrite in sub class to add fields
	}

	/**
	 * @param Field_Section $section
	 *
	 * @return Field_Section
	 */
	public function add_section( Field_Section $section ): Field_Section {
		$this->items[] = $section;

		return $section;
	}

	public function init() {
		$this->add_block();
		$this->add_fields();
	}

	/**
	 * @param Block $block
	 *
	 * @return $this
	 */
	public function set_block( Block $block ): Block_Config {
		$attr = $block->get_attributes();
		if ( empty( $attr[ 'render_callback' ] ) && empty( $attr[ 'render_template' ] ) ) {
			$block->set( 'render_callback', function ( ...$args ) {
				do_action( 'tribe/project/block/render', ...$args );
			} );
		}

		$this->block = $block;

		return $this;
	}

	public function get_block() {
		return $this->block;
	}

	/**
	 * @param Field $field
	 *
	 * @return Block_Config
	 */
	public function add_field( Field $field ): Block_Config {
		$this->items[] = $field;

		return $this;
	}

	/**
	 * @return Group
	 */
	public function get_field_group() {
		if ( static::NAME == '' ) {
			throw new \InvalidArgumentException( "Block requires a NAME constant in " . static::class );
		}

		$group = new Group( static::NAME, [
			'block' => [ static::NAME ],
		] );

		foreach ( $this->items as $block_item ) {
			if ( $block_item instanceof Field_Section ) {
				$group->add_field( $block_item->get_section_field() );
				foreach ( $block_item->get_fields() as $field ) {
					$group->add_field( $field );
				}
				continue;
			}
			$group->add_field( $block_item );
		}

		return $group;
	}

}
