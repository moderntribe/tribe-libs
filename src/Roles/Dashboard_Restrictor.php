<?php

namespace Tribe\Libs\Roles;

class Dashboard_Restictor {

	/**
	 * Restricts the admin dashboard from roles without the required capability.
	 *
	 * @action admin_init 0 0
	 * @see \Tribe\Libs\Roles\Roles_Provider::restrict_admin_dashboard
	 */
	public function check_user() {
		if ( ! current_user_can( Capabilities::VIEW_ADMIN_DASHBOARD ) ) {
			$redirect_url = get_site_url();

			// Provide some context on why the redirect happened
			$redirect_url = add_query_arg( 'unauthorized', '', $redirect_url );

			wp_redirect( $redirect_url );
			exit;
		}
	}

}