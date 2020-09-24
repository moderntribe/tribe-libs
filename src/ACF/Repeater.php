<?php


namespace Tribe\Libs\ACF;

class Repeater extends Field implements ACF_Aggregate {
	protected $key_prefix = 'field';

	/** @var Field[] */
	protected $fields = [ ];

	public function __construct( $key, $attributes = [] ) {
		parent::__construct( $key, $attributes );
		$this->attributes[ 'type' ] = 'repeater';
	}

	public function add_field( Field $field ) {
		$this->fields[] = $field;

		return $this;
	}

	public function get_attributes() {
		$attributes = parent::get_attributes();
		$attributes[ 'sub_fields' ] = [ ];
		foreach ( $this->fields as $f ) {
			$attributes[ 'sub_fields' ][] = $f->get_attributes();
		}
		return $attributes;
	}
}
