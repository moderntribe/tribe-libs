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
		return array_map( static function ( Field $f ) {
			return $f->get_attributes();
		}, $this->fields );
	}

}
