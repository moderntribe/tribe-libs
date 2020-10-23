<?php

namespace Tribe\Libs\Media\Svg;

class Set_Attachment_MetadataTest extends \Codeception\TestCase\WPTestCase {
	/**
	 * @var \IntegrationTester
	 */
	protected $tester;
	private   $_wp_additional_image_sizes;

	protected function setUp(): void {
		parent::setUp();
		$this->backup_image_sizes();
	}

	protected function tearDown(): void {
		$this->restore_image_sizes();
		parent::tearDown();
	}

	private function backup_image_sizes(): void {
		$this->_wp_additional_image_sizes = $GLOBALS['_wp_additional_image_sizes'];
	}

	private function restore_image_sizes(): void {
		if ( $this->_wp_additional_image_sizes ) {
			$GLOBALS['_wp_additional_image_sizes'] = $this->_wp_additional_image_sizes;
			unset( $this->_wp_additional_image_sizes );
		}
	}

	/**
	 * @return void
	 * @dataProvider sample_logos
	 */
	public function test_sets_size_metadata( string $filename ): void {
		$image_sizes = [
			'test_large'         => [ 2000, 2000, false ],
			'test_large_cropped' => [ 2000, 2000, true ],
			'test_small'         => [ 100, 100, false ],
			'test_small_cropped' => [ 100, 100, true ],
		];

		foreach ( $image_sizes as $key => $params ) {
			add_image_size( $key, ... $params );
		}

		// set up our filter
		$enable = new Enable_Uploads();
		$setter = new Set_Attachment_Metadata();
		add_filter( 'wp_generate_attachment_metadata', [ $setter, 'generate_metadata' ], 10, 2 );
		add_filter( 'mime_types', [ $enable, 'set_svg_mimes' ], 10, 1 );
		add_filter( 'upload_mimes', [ $enable, 'set_svg_mimes' ], 10, 1 );

		// create the test file
		// intrinsic width: 146
		// intrinsic height: 106
		$attachment_id = self::factory()->attachment->create_upload_object( $filename );

		// get the full and resized image dimensions
		$resized = [
			'full' => wp_get_attachment_image_src( $attachment_id, 'full' ),
		];
		foreach ( array_keys( $image_sizes ) as $size ) {
			$resized[ $size ] = wp_get_attachment_image_src( $attachment_id, $size );
		}
		$resized = array_map( static function ( array $img ) {
			return [ $img[1], $img[2] ];
		}, $resized );

		// clean up
		wp_delete_attachment( $attachment_id, true );
		remove_filter( 'wp_generate_attachment_metadata', [ $setter, 'generate_metadata' ], 10 );
		remove_filter( 'mime_types', [ $enable, 'set_svg_mimes' ], 10 );
		remove_filter( 'upload_mimes', [ $enable, 'set_svg_mimes' ], 10 );

		self::assertEquals( [ 146, 106 ], $resized['full'] );
		self::assertEquals( [ 2000, 1452 ], $resized['test_large'] );
		self::assertEquals( [ 2000, 2000 ], $resized['test_large_cropped'] );
		self::assertEquals( [ 100, 73 ], $resized['test_small'] );
		self::assertEquals( [ 100, 100 ], $resized['test_small_cropped'] );
	}

	public function sample_logos(): array {
		return [
			[ codecept_data_dir( 'media/logo.svg' ) ],
			[ codecept_data_dir( 'media/logo.svgz' ) ],
		];
	}
}
