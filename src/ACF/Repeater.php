<?php


namespace Tribe\Libs\ACF;

class Repeater extends Sub_Field implements ACF_Aggregate {
	protected $key_prefix = 'field';

	public function __construct( $key, $attributes = [] ) {
		parent::__construct( $key, $attributes );
		$this->attributes[ 'type' ] = 'repeater';
	}
}
