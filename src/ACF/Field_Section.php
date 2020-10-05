<?php
declare( strict_types=1 );

namespace Tribe\Libs\ACF;

class Field_Section extends Field implements ACF_Aggregate {
	/**
	 * @var Field_Collection
	 */
	protected $fields;

	/**
	 * @var string
	 */
	protected $key_prefix = 'section_';

	/**
	 * Field_Section constructor.
	 *
	 * @param string $name
	 * @param string $label
	 * @param string $type
	 */
	public function __construct( string $name, string $label, string $type ) {
		parent::__construct( $name, [
			'type'  => $type,
			'label' => $label,
			'name'  => $name,
		] );
		$this->fields = new Field_Collection();
	}

	/**
	 * @param Field $field
	 *
	 * @return $this
	 */
	public function add_field( Field $field ): Field_Section {
		$this->fields->append( $field );

		return $this;
	}

	/**
	 * @return Field_Collection
	 */
	public function get_fields(): Field_Collection {
		return $this->fields;
	}

	/**
	 * @return array
	 */
	public function get_attributes() {
		$field_attributes   = [];
		$field_attributes[] = $this->attributes;

		/** @var Field $field */
		foreach ( $this->fields as $field ) {
			$field_attributes = array_merge($field_attributes, $field->get_attributes());
		}

		return $field_attributes;
	}

}
