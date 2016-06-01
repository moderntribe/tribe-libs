<?php

namespace Tribe\Libs\Post_Type;

use Tribe\Libs\Post_Type\Meta_Box_Handlers\CMB2 as CMB2_Meta_Box_Handler;
use Tribe\Libs\Post_Type\Meta_Box_Handlers\Meta_Box_Handler_Interface;

class Post_Type_Registration {

	/**
	 * Registers a post type
	 *
	 * @param Post_Type_Config           $config
	 * @param Meta_Box_Handler_Interface $meta_box_handler
	 */
	public static function register( Post_Type_Config $config, Meta_Box_Handler_Interface $meta_box_handler = null ) {
		$meta_box_handler = null !== $meta_box_handler ? $meta_box_handler : new CMB2_Meta_Box_Handler();
		$callback         = self::build_registration_callback( $config, $meta_box_handler );

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
	 * @param Post_Type_Config           $config
	 * @param Meta_Box_Handler_Interface $meta_box_handler
	 *
	 * @return \Closure
	 */
	private static function build_registration_callback( Post_Type_Config $config, Meta_Box_Handler_Interface $meta_box_handler ) {
		return function () use ( $config, $meta_box_handler ) {
			if ( empty( $config->post_type() ) ) {
				throw new \RuntimeException( 'Invalid configuration. Specify a post type.' );
			}
			register_extended_post_type( $config->post_type(), $config->get_args(), $config->get_labels() );

			$meta_box_handler->register_meta_boxes( $config );
		};
	}
}
