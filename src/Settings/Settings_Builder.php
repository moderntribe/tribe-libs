<?php declare(strict_types=1);

namespace Tribe\Libs\Settings;

/**
 * Interface Settings_Builder
 *
 * @package Tribe\Project\Settings
 */
interface Settings_Builder {

	/**
	 * Return the title of the settings screen
	 */
	public function get_title(): string;

	/**
	 * Return the cap the current user needs to have to be able to see this settings screen
	 */
	public function get_capability(): string;

	/**
	 * Return slug of the parent menu where you want the settings page
	 */
	public function get_parent_slug(): string;

	/**
	 * Register the settings screen in WordPress
	 */
	public function register_settings(): void;

	/**
	 * Return the setting value for a given Key.
	 * Return $default if the value is empty.
	 *
	 * @param string     $key
	 * @param mixed|null $default
	 *
	 * @return mixed
	 */
	public function get_setting( string $key, $default = null );

}
