<?php

namespace Tribe\Libs\ACF;

class Group extends ACF_Configuration implements ACF_Aggregate {
	protected $key_prefix = 'group';

	/** @var Field[] */
	protected $fields = [ ];

	protected $post_types = [ ];

	public function add_field( Field $field ) {
		$this->fields[] = $field;
	}

	public function get_attributes() {
		$attributes = parent::get_attributes();
		$attributes[ 'fields' ] = [ ];
		foreach ( $this->fields as $f ) {
			$attributes[ 'fields' ][] = $f->get_attributes();
		}

		foreach ( $this->post_types as $post_type ) {
			$attributes[ 'location' ][] = [
				[
					'param'    => 'post_type',
					'operator' => '==',
					'value'    => $post_type,
				],
			];
		}
		return $attributes;
	}

	/**
	 * Set the post types in which this group will be available
	 *
	 * @param array $post_types
	 * @return void
	 */
	public function set_post_types( array $post_types ) {
		$this->post_types = $post_types;
	}
}