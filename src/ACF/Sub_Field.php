<?php

namespace Tribe\Libs\ACF;

class Sub_Field extends Field {
	/** @var Field[] */
	protected $fields = [];

	/**
	 * @param Field $field
	 *
	 * @return Sub_Field
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
