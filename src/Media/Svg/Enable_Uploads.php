<?php
declare( strict_types=1 );

namespace Tribe\Libs\Media\Svg;

/**
 * Class Enable_Uploads
 *
 * Filters WP to enable SVG file uploads
 */
class Enable_Uploads {

	/**
	 * @param array $mimes
	 *
	 * @return array
	 * @filter mime_types
	 * @filter upload_mimes
	 */
	public function set_svg_mimes( array $mimes ): array {
		$mimes['svg|svgz'] = 'image/svg+xml';

		return $mimes;
	}

	/**
	 * Fixes the issue in WordPress 4.7.1 being unable to correctly identify SVGs
	 *
	 * @param array       $data
	 * @param string      $file
	 *
	 * @return array
	 * @filter wp_check_filetype_and_ext
	 */
	public function set_upload_mime( array $data, string $file ): array {
		$ext = $data['ext'] ?? '';
		if ( empty( $ext ) ) {
			$ext = strtolower( pathinfo( $file, PATHINFO_EXTENSION ) );
		}
		if ( $ext === 'svg' ) {
			$data['type'] = 'image/svg+xml';
			$data['ext']  = $ext;
		} elseif ( $ext === 'svgz' ) {
			$data['type'] = 'image/svg+xml';
			$data['ext']  = $ext;
		}

		return $data;
	}
}
