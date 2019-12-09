<?php

namespace Tribe\Libs\Roles;

use Tribe\Libs\Roles\Role\Role;

use WP_User;

class Login_Redirector {

	private $roles;

	/**
	 * Login_Redirector constructor.
	 *
	 * @param array $roles Array of concrete instances of \Tribe\Libs\Roles\Role\Role
	 */
	public function __construct( array $roles ) {
		$this->roles = $roles;
	}

	/**
	 * Maybe overrides the login redirection URL for this Role
	 *
	 * @filter login_redirect 10 3
	 *
	 * @param $redirect_to
	 * @param $requested_redirect_to
	 * @param $user
	 *
	 * @return string
	 * @see \Tribe\Libs\Roles\Roles_Provider::redirect_after_login
	 */
	public function redirect_after_login( $redirect_to, $requested_redirect_to, $user ) {
		// Early bail: no user available
		if ( ! $user instanceof WP_User ) {
			return $requested_redirect_to;
		}

		// Try to find a matching Role for this user
		/** @var Role $role */
		foreach ( $this->roles as $role ) {
			// Early bail: user does have this role
			if ( ! $user->has_cap( $role->get_slug() ) ) {
				return $requested_redirect_to;
			}

			// Early bail: role does not overrides login redirection
			if ( empty( $role->url_to_redirect_after_login() ) ) {
				return $requested_redirect_to;
			} else {
				return $role->url_to_redirect_after_login();
			}
		}

		// Couldn't find any override. Let's just return the default behavior
		return $requested_redirect_to;
	}

}