<?php


namespace Tribe\Libs\ACF;

class Field extends ACF_Configuration {
	protected $key_prefix = 'field';

	public function __construct( $key, $attributes = []) {
		$this->attributes = $attributes;
		parent::__construct( $key );
	}
}
