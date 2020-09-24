<?php


namespace Tribe\Libs\ACF;

class Field_Group extends Sub_Field implements ACF_Aggregate {
	protected $key_prefix = 'field';

	/**
	 * @param string $key
	 * @param array  $attributes
	 */
	public function __construct( $key, $attributes = [] ) {
		parent::__construct( $key, $attributes );
		$this->attributes[ 'type' ] = 'group';
	}
}
