<?php


namespace Tribe\Libs\ACF;

use Tribe\Libs\ACF\Traits\With_Sub_Fields;

class Layout extends Field implements ACF_Aggregate {
	use With_Sub_Fields;
	protected $key_prefix = 'layout';

	public function get_attributes() {
		$attributes                 = parent::get_attributes();
		$attributes[ 'sub_fields' ] = $this->get_sub_field_attributes();

		return $attributes;
	}

}
