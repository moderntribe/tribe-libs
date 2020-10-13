<?php
declare( strict_types=1 );

namespace Tribe\Libs\Media;

use Tribe\Libs\Container\Abstract_Subscriber;
use Tribe\Libs\Media\Svg\Enable_Uploads;
use Tribe\Libs\Media\Svg\Sanitize_Uploads;
use Tribe\Libs\Media\Svg\Set_Attachment_Metadata;

class Media_Subscriber extends Abstract_Subscriber {
	public function register(): void {
		$this->full_size_gif();
		$this->svg_uploads();
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

	private function svg_uploads(): void {
		if ( defined( 'TRIBE_ENABLE_SVG_UPLOADS' ) && TRIBE_ENABLE_SVG_UPLOADS === false ) {
			return;
		}

		add_filter( 'mime_types', function ( $mimes ) {
			return $this->container->get( Enable_Uploads::class )->set_svg_mimes( $mimes );
		}, 10, 1 );

		add_filter( 'wp_check_filetype_and_ext', function ( $data, $file ) {
			return $this->container->get( Enable_Uploads::class )->set_upload_mime( $data, $file );
		}, 10, 2 );

		add_filter( 'wp_handle_upload_prefilter', function ( $file ) {
			return $this->container->get( Sanitize_Uploads::class )->filter_svg_uploads( $file );
		}, 10, 1 );

		add_filter( 'wp_generate_attachment_metadata', function ( $metadata, $attachment_id ) {
			return $this->container->get( Set_Attachment_Metadata::class )->generate_metadata( $metadata, (int) $attachment_id );
		}, 10, 2 );
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
