<?php
declare( strict_types=1 );

namespace Tribe\Libs\ACF;

abstract class Block_Config {
	public const NAME        = '';
	public const CONTENT_TAB = 'content';
	public const SETTING_TAB = 'settings';

	/**
	 * @var Field_Collection
	 */
	protected $fields;

	/**
	 * @var Field_Collection
	 */
	protected $settings;

	/**
	 * @var Block
	 */
	protected $block;

	abstract public function add_block();

	protected function add_fields() {
		//overwrite in sub class to add fields
	}

	protected function add_settings() {
		//overwrite in sub class to add settings
	}

	public function init() {
		$this->fields   = new Field_Collection();
		$this->settings = new Field_Collection();
		$this->add_block();
		$this->add_fields();
		$this->add_settings();
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
		$this->fields->append( $field );

		return $this;
	}

	/**
	 * @param Field $field
	 *
	 * @return Block_Config
	 */
	public function add_setting( Field $field ): Block_Config {
		$this->settings->append( $field );

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

		$group->add_field( $this->get_tab(
			self::CONTENT_TAB,
			__( 'Content', 'tribe' )
		) );

		foreach ( $this->fields as $field ) {
			$group->add_field( $field );
		}

		if ( ! $this->settings->count() ) {
			return $group;
		}

		$group->add_field( $this->get_tab(
			self::SETTING_TAB,
			__( 'Settings', 'tribe' )
		) );

		foreach ( $this->settings as $setting ) {
			$group->add_field( $setting );
		}

		return $group;
	}

	/**
	 * @param string $key
	 * @param string $label
	 *
	 * @return Field
	 */
	protected function get_tab( $key, $label ): Field {
		return new Field( $key, [
			'label'     => $label,
			'name'      => $key,
			'type'      => 'tab',
			'placement' => 'top',
		] );
	}

}
