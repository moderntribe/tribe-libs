<?php
declare( strict_types=1 );

namespace Tribe\Libs\Media;

use Tribe\Libs\Container\Abstract_Subscriber;

class Media_Subscriber extends Abstract_Subscriber {
	public function register(): void {
		$this->full_size_gif();
		$this->svg_sizes();
		$this->disable_responsive_images();
	}

	private function full_size_gif(): void {
		if ( defined( 'FORCE_FULL_SIZE_GIFS' ) && FORCE_FULL_SIZE_GIFS === false ) {
			return;
		}
		add_filter( 'image_downsize', function ( $data, $id, $size ) {
			return $this->container->get( Full_Size_Gif::class )->full_size_only_gif( $data, $id, $size );
		}, 10, 3 );
	}

	private function svg_sizes(): void {
		add_filter( 'wp_get_attachment_image_src', function ( $image, $attachment_id, $size, $icon ) {
			return $this->container->get( SVG_Sizes::class )->set_accurate_sizes( $image, $attachment_id, $size, $icon );
		}, 11, 4 );
	}

	private function disable_responsive_images(): void {
		if ( defined( 'DISABLE_WP_RESPONSIVE_IMAGES' ) && DISABLE_WP_RESPONSIVE_IMAGES === false ) {
			return;
		}
		add_filter( 'wp_get_attachment_image_attributes', function ( $attr ) {
			return $this->container->get( WP_Responsive_Image_Disabler::class )->filter_image_attributes( $attr );
		}, 999, 1 );
		add_action( 'after_setup_theme', function () {
			$this->container->get( WP_Responsive_Image_Disabler::class )->disable_wordpress_filters();
		}, 10, 0 );
	}
}
