<?php

namespace Tribe\Libs\Roles\Role;

class Author extends Role {
	/**
	 * @inheritDoc
	 */
	public function get_slug(): string {
		return 'author';
	}

	/**
	 * @inheritDoc
	 */
	public function get_display_name(): string {
		return 'Author';
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