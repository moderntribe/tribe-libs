<?php

namespace Tribe\Libs\Post_Meta;

use Tribe\Libs\Object_Meta\Meta_Group;

/**
 * Class Meta_Map
 *
 * Maps requests for meta keys to the Meta_Group responsible for handling it
 */
class Meta_Map {

	/**
	 * Existing Square One projects are passing this in, so removing it would
	 * be a breaking change.
	 *
	 * @var string
	 *
	 * @phpstan-ignore-next-line
	 */
	private $post_type = '';

	/** @var Meta_Group[] */
	private $keys = [];

	public function __construct( $post_type ) {
		$this->post_type = $post_type;
	}

	/**
	 * Add the Meta_Group as the handler for its declared keys.
	 * Any keys that are already handled will be taken over by
	 * this group.
	 *
	 * @param Meta_Group $group
	 * @return void
	 */
	public function add( Meta_Group $group ) {
		foreach ( $group->get_keys() as $key ) {
			$this->keys[ $key ] = $group;
		}
	}

	/**
	 * @return array All the keys that will be mapped
	 */
	public function get_keys() {
		return array_keys( $this->keys );
	}

	/**
	 * @param int $post_id
	 * @param string $key
	 * @return mixed The value for the given key
	 */
	public function get_value( $post_id, $key ) {
		if ( isset( $this->keys[ $key ] ) ) {
			return $this->keys[ $key ]->get_value( $post_id, $key );
		}
		return null;
	}
}
