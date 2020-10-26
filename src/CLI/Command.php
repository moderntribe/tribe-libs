<?php

namespace Tribe\Libs\CLI;

use WP_CLI;

abstract class Command extends \WP_CLI_Command implements Command_Interface {

	public function register() {
		WP_CLI::add_command( 's1 ' . $this->command(), [ $this, 'run_command' ], [
			'shortdesc' => $this->description(),
			'synopsis'  => $this->arguments(),
		] );
	}

	abstract protected function command();
	abstract protected function description();
	abstract protected function arguments();

	/**
	 * Frees up memory for long running processes.
	 *
	 * @return void
	 */
	protected function stop_the_insanity() : void {
		global $wpdb, $wp_actions, $wp_filter, $wp_object_cache;

		// Reset queries.
		$wpdb->queries = array();

		// Prevent wp_actions from growing out of control.
		$wp_actions = array(); // @codingStandardsIgnoreLine
		if ( is_object( $wp_object_cache ) ) {
			$wp_object_cache->group_ops      = array();
			$wp_object_cache->stats          = array();
			$wp_object_cache->memcache_debug = array();
			$wp_object_cache->cache          = array();
			if ( method_exists( $wp_object_cache, '__remoteset' ) ) {
				$wp_object_cache->__remoteset();
			}
		}

		/**
		 * The WP_Query class hooks a reference to one of its own methods
		 * onto filters if update_post_term_cache or
		 * update_post_meta_cache are true, which prevents PHP's garbage
		 * collector from cleaning up the WP_Query instance on long-
		 * running processes.
		 *
		 * By manually removing these callbacks (often created by things
		 * like get_posts()), we're able to properly unallocate memory
		 * once occupied by a WP_Query object.
		 */
		if ( isset( $wp_filter['get_term_metadata'] ) ) {
			/*
			 * WordPress 4.7 has a new Hook infrastructure, so we need to make sure
			 * we're accessing the global array properly.
			 */
			if ( class_exists( 'WP_Hook' ) && $wp_filter['get_term_metadata'] instanceof \WP_Hook ) {
				$filter_callbacks = &$wp_filter['get_term_metadata']->callbacks;
			} else {
				$filter_callbacks = &$wp_filter['get_term_metadata'];
			}
			if ( isset( $filter_callbacks[10] ) ) {
				foreach ( $filter_callbacks[10] as $hook => $content ) {
					if ( preg_match( '#^[0-9a-f]{32}lazyload_term_meta$#', $hook ) ) {
						unset( $filter_callbacks[10][ $hook ] );
					}
				}
			}
		}
	}

}
