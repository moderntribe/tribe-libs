<?php

namespace Tribe\Libs\ACF\Traits;

use Tribe\Libs\ACF\Field;

trait With_Sub_Fields {
	/** @var Field[] */
	protected $fields = [];

	/**
	 * @param Field $field
	 *
	 * @return static
	 */
	public function add_field( Field $field ) {
		$this->fields[] = $field;

		return $this;
	}

	public function get_sub_field_attributes(): array {
		$field_attributes = [];

		/** @var Field $field */
		foreach ( $this->fields as $field ) {
			$field_attributes = array_merge( $field_attributes, $field->get_attributes() );
		}

		return $field_attributes;
	}

}
