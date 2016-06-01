<?php

namespace Tribe\Libs\Post_Type\Meta_Box_Handlers;


use Tribe\Libs\Post_Type\Post_Type_Config;

class CMB2 implements Meta_Box_Handler_Interface {

	/**
	 * Registers the meta boxes for a post type.
	 *
	 * @param Post_Type_Config $config
	 */
	public function register_meta_boxes( Post_Type_Config $config ) {
		add_filter( 'cmb2_meta_boxes', function ( $meta_boxes ) use ( $config ) {
			$post_type_meta_boxes = $config->get_meta_boxes();
			$post_type_meta_boxes = apply_filters( "tribe_{$config->post_type()}_meta_boxes", $post_type_meta_boxes );
			$meta_boxes           = array_merge( $meta_boxes, $post_type_meta_boxes );

			return $meta_boxes;
		} );
	}
}