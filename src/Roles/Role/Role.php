<?php

namespace Tribe\Libs\Roles\Role;

use Tribe\Libs\Roles\Capabilities;
use WP_Role;
use WP_User;

abstract class Role {
	/**
	 * Get the role slug. Example: "super-admin"
	 *
	 * @return string
	 */
	abstract public function get_slug(): string;

	/**
	 * Get the role display name. Example: "Super Admin"
	 *
	 * @return string
	 */
	abstract public function get_display_name(): string;

	/**
	 * Determines if given Role can access the wp-admin dashboard
	 *
	 * @return bool
	 */
	abstract public function can_view_admin_dashboard(): bool;

	/**
	 * Returns an array of new capabilities for this role.
	 *
	 * If the role already exists, the existing capabilities
	 * will be preserved and these will be added to the Role.
	 *
	 * @return array
	 */
	abstract public function get_new_capabilities(): array;

	/**
	 * Returns an array of capabilities to be removed for this role.
	 *
	 * @return array
	 */
	abstract public function get_capabilities_to_remove(): array;

	/**
	 * Redirects the user to this URL after login.
	 * If empty, won't redirect.
	 *
	 * @return string
	 */
	public function url_to_redirect_after_login(): string {
		return '';
	}

	/**
	 * Register roles.
	 * If Role exists, it will update capabilities.
	 */
	final public function register() {
		$slug = $this->get_slug();
		$role = get_role( $slug );

		if ( $role instanceof WP_Role ) {
			$this->update_existing_role( $role );
		} else {
			$this->add_new_role();
		}
	}

	private function update_existing_role( WP_Role $role ) {
		foreach ( $this->get_new_capabilities() as $new_capability ) {
			$role->add_cap( $new_capability );
		}

		foreach ( $this->get_capabilities_to_remove() as $cap ) {
			$role->remove_cap( $cap );
		}

		if ( $this->can_view_admin_dashboard() ) {
			$role->add_cap( Capabilities::VIEW_ADMIN_DASHBOARD );
		} else {
			$role->remove_cap( Capabilities::VIEW_ADMIN_DASHBOARD );
		}
	}

	private function add_new_role() {
		$new_role = add_role( $this->get_slug(), $this->get_display_name(), $this->get_new_capabilities() );

		if ( $new_role instanceof WP_Role ) {
			foreach ( $this->get_capabilities_to_remove() as $cap ) {
				$new_role->remove_cap( $cap );
			}
			if ( $this->can_view_admin_dashboard() ) {
				$new_role->add_cap( Capabilities::VIEW_ADMIN_DASHBOARD );
			} else {
				$new_role->remove_cap( Capabilities::VIEW_ADMIN_DASHBOARD );
			}
		}
	}
}