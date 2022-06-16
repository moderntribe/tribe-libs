<?php declare(strict_types=1);

namespace Tribe\Libs\ACF\Traits;

use Tribe\Libs\ACF\ACF_Configuration;
use Tribe\Libs\ACF\Field;

/**
 * @mixin Field
 */
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

	/**
	 * Get the attributes of all subfields.
	 *
	 * @return array[]
	 */
	public function get_sub_field_attributes(): array {
		return array_merge( ... array_map( static function ( ACF_Configuration $field ) {
			return $field->get_attributes();
		}, $this->fields ) );
	}

}
