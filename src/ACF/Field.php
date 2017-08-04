<?php


namespace Tribe\Libs\ACF;


class Field extends ACF_Configuration {
	protected $key_prefix = 'field';

	public function get_field( $id, $context = 'post' ) {
		switch( $context ) {
			case 'post':
				return get_post_meta( $id, $this->key, true );
			case 'user':
				return get_user_meta( $id, $this->key, true );
			default:
				break;
		}
	}
}