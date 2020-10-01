<?php
declare( strict_types=1 );

namespace Tribe\Libs\ACF;

class Field_Section {
	/**
	 * @var string
	 */
	protected $type;

	/**
	 * @var string
	 */
	protected $label;

	/**
	 * @var Field_Collection
	 */
	protected $fields;

	/**
	 * Field_Section constructor.
	 *
	 * @param string $label
	 * @param string $type
	 */
	public function __construct( string $label, string $type ) {
		$this->label  = $label;
		$this->type   = $type;
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
	 * @return Field
	 */
	public function get_section_field() {
		//Since a section is really a faux field, we don't really need a real name for it.
		return new Field( uniqid( 'section-' ), [
			'type'  => $this->type,
			'label' => $this->label,
		] );
	}

}