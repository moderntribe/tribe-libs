<?php


namespace Tribe\Libs\ACF;

class Field_Group extends Field implements ACF_Aggregate {
	protected $key_prefix = 'field';

	/** @var Field[] */
	protected $fields = [];

	/**
	 * @param string $key
	 * @param array  $attributes
	 */
	public function __construct( $key, $attributes = [] ) {
		parent::__construct( $key, $attributes );
		$this->attributes[ 'type' ] = 'group';
	}

	/**
	 * @param Field $field
	 *
	 * @return Field_Group
	 */
	public function add_field( Field $field ) {
		$this->fields[] = $field;

		return $this;
	}

	/**
	 * @return array
	 */
	public function get_attributes(): array {
		$attributes                 = parent::get_attributes();
		$attributes[ 'sub_fields' ] = [];
		foreach ( $this->fields as $f ) {
			$attributes[ 'sub_fields' ][] = $f->get_attributes();
		}

		return $attributes;
	}
}
