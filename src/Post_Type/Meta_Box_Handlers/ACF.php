<?php

namespace Tribe\Libs\Post_Type\Meta_Box_Handlers;


use Tribe\Libs\Post_Type\Post_Type_Config;

class ACF implements Meta_Box_Handler_Interface {

	/**
	 * Registers the meta boxes for a post type.
	 *
	 * @param Post_Type_Config $config
	 */
	public function register_meta_boxes( Post_Type_Config $config ) {
		if ( ! function_exists( 'register_field_group' ) ) {
			return;
		}

		acf_add_local_field_group( $config->get_meta_boxes() );
	}
}