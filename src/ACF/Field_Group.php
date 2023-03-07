<?php declare(strict_types=1);

namespace Tribe\Libs\ACF;

use Tribe\Libs\ACF\Contracts\Has_Sub_Fields;
use Tribe\Libs\ACF\Traits\With_Sub_Fields;

class Field_Group extends Field implements ACF_Aggregate, Has_Sub_Fields {

	use With_Sub_Fields;

	protected $key_prefix = 'field';

	/**
	 * @param string $key
	 * @param array  $attributes
	 */
	public function __construct( $key, $attributes = [] ) {
		parent::__construct( $key, $attributes );
		$this->attributes[ 'type' ] = 'group';
	}

	public function get_attributes() {
		$attributes                 = $this->attributes;
		$attributes[ 'sub_fields' ] = $this->get_sub_field_attributes();

		return [ $attributes ];
	}

}
