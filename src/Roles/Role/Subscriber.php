<?php

namespace Tribe\Libs\Roles\Role;

class Subscriber extends Role {
	/**
	 * @inheritDoc
	 */
	public function get_slug(): string {
		return 'subscriber';
	}

	/**
	 * @inheritDoc
	 */
	public function get_display_name(): string {
		return 'Subscriber';
	}

	/**
	 * @inheritDoc
	 */
	public function can_view_admin_dashboard(): bool {
		return true;
	}

	/**
	 * @inheritDoc
	 */
	public function get_new_capabilities(): array {
		return [];
	}

	/**
	 * @inheritDoc
	 */
	public function get_capabilities_to_remove(): array {
		return [];
	}
}