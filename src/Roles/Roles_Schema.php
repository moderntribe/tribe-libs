<?php

namespace Tribe\Libs\Roles;

use Tribe\Libs\Schema\Schema;
use Tribe\Libs\Roles\Role\Role;

class Roles_Schema extends Schema {
	/**
	 * @var int $schema_version Bump this whenever you make changes to Roles
	 */
	protected $schema_version = 1;

	private $roles = [];

	public function __construct( array $roles ) {
		parent::__construct();
		foreach ( $roles as $role ) {
			if ( is_a( $role, Role::class, true ) ) {
				$this->roles[] = $role;
			}
		}
	}

	/**
	 * @inheritDoc
	 */
	protected function get_updates() {
		return [
			$this->schema_version => function () {
				/** @var Role $role */
				foreach ( $this->roles as $role ) {
					$role->register();
				};
			},
		];
	}
}