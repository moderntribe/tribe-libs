<?php declare(strict_types=1);

namespace Tribe\Libs\ACF;

class Field extends ACF_Configuration {

	protected $key_prefix = 'field';

	public function __construct( $key, $attributes = []) {
		$this->attributes = $attributes;
		parent::__construct( $key );
	}

}
