<?php


namespace Tribe\Libs\Schema;

/**
 * Class Capabilities
 *
 * @package Tribe\Libs\Schools
 *
 * A utililty class for registering capabilities for a post type
 */
class Capabilities {
	private $cap_prefixes = array(
		'editor' => array(
			'create',
			'read',
			'read_private',
			'edit',
			'edit_others',
			'edit_private',
			'edit_published',
			'delete',
			'delete_others',
			'delete_private',
			'delete_published',
			'publish',
		),
		'author' => array(
			'create',
			'read',
			'edit',
			'edit_published',
			'delete',
			'delete_published',
			'publish',
		),
		'contributor' => array(
			'create',
			'read',
			'edit',
			'delete',
		),
		'subscriber' => array(
			'read',
		),
	);

	public function register_post_type_caps( $post_type, $role_id, $level = 'editor' ) {
		if ( !isset( $this->cap_prefixes[$level] ) ) {
			return FALSE;
		}
		$role = get_role( $role_id );
		if ( !$role ) {
			return FALSE;
		}
		foreach ( $this->cap_prefixes[$level] as $prefix ) {
			$role->add_cap( $prefix . '_' . $post_type );
		}
		return TRUE;
	}
} 