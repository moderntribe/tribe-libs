<?php


namespace Tribe\Libs\ACF;

use Tribe\Libs\ACF\Traits\With_Sub_Fields;

class Repeater extends Field implements ACF_Aggregate {
	use With_Sub_Fields;

	protected $key_prefix = 'field';

	public function __construct( $key, $attributes = [] ) {
		parent::__construct( $key, $attributes );
		$this->attributes[ 'type' ] = 'repeater';
	}

	public function get_attributes() {
		$attributes                 = parent::get_attributes();
		$attributes[ 'sub_fields' ] = $this->get_sub_field_attributes();

		return $attributes;
	}

}
