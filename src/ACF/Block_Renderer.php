<?php

namespace Tribe\Libs\ACF;

class Block_Renderer {

	public const THEME_PATH_TEMPLATE = 'blocks/%1$s/%1$s'; //blocks/hero/hero.php

	/**
	 * @param  array   $block       The block attributes.
	 * @param  string  $content     The block content.
	 * @param  bool    $is_preview  The block preview context.
	 * @param  int     $post_id     The current post ID.
	 *
	 * @return void
	 */
	public function render_template( $block, $content, $is_preview, $post_id ) {
		$name      = str_replace( 'acf/', '', $block['name'] );

		/**
		 * Allow the file path to be dynamically changed.
		 *
		 * @param  string  $file_path   The server path with the slug to the template, e.g. 'components/blocks/accordion/accordion'
		 * @param  string  $theme_path  The server path to the directory we're looking for the template in, e.g. 'components/blocks/accordion'
		 * @param  string  $slug        The template slug, e.g. 'accordion'
		 * @param  array   $block       The block arguments.
		 * @param  bool    $is_preview  The block preview context.
		 * @param  int     $post_id     The current post id.
		 */
		$file_path = (string) apply_filters( 'tribe/project/block/template_path', sprintf( self::THEME_PATH_TEMPLATE, $name ), self::THEME_PATH_TEMPLATE, $name, $block, $is_preview, $post_id );
		$path      = locate_template( $file_path . '.php' );

		if ( ! file_exists( $path ) ) {
			if ( ! WP_DEBUG ) {
				return;
			}
			echo '<pre>';
			print_r( $block );
			print_r( $content );
			print_r( $is_preview );
			print_r( $post_id );
			echo '</pre>';

			return;
		}

		/**
		 * Allow additional arguments to be passed to block templates.
		 *
		 * @param  array  $args        The arguments passed to the template.
		 * @param  array  $block       The block arguments.
		 * @param  bool   $is_preview  The block preview context.
		 * @param  int    $post_id     The current post id.
		 */
		$args = apply_filters( 'tribe/project/block/template/args', [ 'block' => $block ], $block, $is_preview, $post_id );

		get_template_part( $file_path, null, $args );
	}

}
