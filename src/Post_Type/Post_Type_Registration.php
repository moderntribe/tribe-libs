<?php

namespace Tribe\Libs\Post_Type;

class Post_Type_Registration {
	/**
	 * Registers a post type
	 *
	 * @param Post_Type_Config $config
	 */
	public static function register( Post_Type_Config $config ) {
		$callback = self::build_registration_callback( $config );

		// do not register until init
		if ( did_action( 'init' ) ) {
			$callback();
		} else {
			add_action( 'init', $callback, 10, 0 );
		}
	}

	/**
	 * Build the callback that will register a post type using the given config
	 *
	 * @param Post_Type_Config $config
	 * @return \Closure
	 */
	private static function build_registration_callback( Post_Type_Config $config ) {
		return function() use ( $config ) {
			if ( empty( $config->post_type() ) ) {
				throw new \RuntimeException( 'Invalid configuration. Specify a post type.' );
			}
			register_extended_post_type( $config->post_type(), $config->get_args(), $config->get_labels() );
			add_filter( 'cmb2_meta_boxes', function( $meta_boxes ) use ( $config ) {
				$post_type_meta_boxes = $config->get_meta_boxes();
				$post_type_meta_boxes = apply_filters( "tribe_{$config->post_type()}_meta_boxes", $post_type_meta_boxes );
				$meta_boxes = array_merge( $meta_boxes, $post_type_meta_boxes );
				return $meta_boxes;
			});
		};
	}

}