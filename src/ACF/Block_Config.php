<?php
declare( strict_types=1 );

namespace Tribe\Libs\ACF;

abstract class Block_Config {
	public const NAME = '';

	/**
	 * @var array
	 */
	protected $fields = [];

	/**
	 * @var Block
	 */
	protected $block;

	public function __construct() {
		$this->fields = new Field_Collection();
		$this->add_block();
		$this->add_fields();
	}

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
		$this->fields[] = $section;

		return $section;
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
		$this->fields[] = $field;

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

		foreach ( $this->fields as $field ) {
			$group->add_field( $field );
		}

		return $group;
	}

}
