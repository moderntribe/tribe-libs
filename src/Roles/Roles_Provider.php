<?php

namespace Tribe\Libs\Roles;

use Pimple\Container;
use Tribe\Libs\Container\Service_Provider;
use Tribe\Libs\Roles\Role\Administrator;
use Tribe\Libs\Roles\Role\Author;
use Tribe\Libs\Roles\Role\Editor;
use Tribe\Libs\Roles\Role\Subscriber;

class Roles_Provider extends Service_Provider {
	const ROLES = 'users.roles';

	public function register( Container $container ) {
		$container[ self::ROLES ] = function () {
			return [
				new Author,
				new Administrator,
				new Editor,
				new Subscriber,
			];
		};

		$this->update_roles( $container );
		$this->hide_topbar( $container );
		$this->redirect_after_login( $container );
		$this->restrict_admin_dashboard( $container );
	}

	private function update_roles( Container $container ) {
		$container[ Roles_Schema::class ] = function () use ( $container ) {
			return new Roles_Schema( $container[ self::ROLES ] );
		};

		add_action( 'admin_init', function () use ( $container ) {
			if ( $container[ Roles_Schema::class ]->update_required() ) {
				$container[ Roles_Schema::class ]->do_updates();
			}
		}, 0, 0 );
	}

	private function hide_topbar( Container $container ) {
		add_filter( 'show_admin_bar', function (): bool {
			return current_user_can( Capabilities::VIEW_ADMIN_DASHBOARD );
		} );
	}

	private function redirect_after_login( Container $container ) {
		$container[ Login_Redirector::class ] = function () use ( $container ) {
			return new Login_Redirector( $container[ self::ROLES ] );
		};

		add_filter( 'login_redirect', function ( $redirect_to, $requested_redirect_to, $user ) use ( $container ) {
			return $container[ Login_Redirector::class ]->redirect_after_login( $redirect_to, $requested_redirect_to,
				$user );
		}, 10, 3 );
	}

	private function restrict_admin_dashboard( Container $container ) {
		$container[ Dashboard_Restictor::class ] = function () {
			return new Dashboard_Restictor;
		};

		add_action( 'admin_init', function () use ( $container ) {
			$container[ Dashboard_Restictor::class ]->check_user();
		}, 1, 0 );
	}
}
