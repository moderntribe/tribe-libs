<?php declare(strict_types=1);

namespace Tribe\Libs\Media\Svg\Store\Stores;

use Tribe\Libs\Media\Media_Definer;
use Tribe\Libs\Media\Media_Subscriber;
use Tribe\Libs\Tests\Test_Case;

final class SvgMetaStoreTest extends Test_Case {

	protected function setUp(): void {
		parent::setUp();

		$this->init_container( [
			Media_Definer::class,
		], [
			Media_Subscriber::class,
		] );
	}

	public function test_it_stores_and_retrieves_an_uploaded_svg(): void {
		$file          = codecept_data_dir( 'media/test.svg' );
		$attachment_id = $this->factory()->attachment->create( [
			'file'           => $file,
			'post_mime_type' => 'image/svg+xml',
		] );

		$store = $this->c->make( Svg_Meta_Store::class );

		$this->assertTrue( $store->save( get_attached_file( $attachment_id ), $attachment_id ) );
		$this->assertStringEqualsFile( $file, $store->get( $attachment_id ) );
	}

	public function test_it_keeps_xml_tag_in_svg_markup(): void {
		$file          = codecept_data_dir( 'media/test.svg' );
		$attachment_id = $this->factory()->attachment->create( [
			'file'           => $file,
			'post_mime_type' => 'image/svg+xml',
		] );

		$store = $this->c->make( Svg_Meta_Store::class );

		$this->assertTrue( $store->save( get_attached_file( $attachment_id ), $attachment_id ) );
		$this->assertEquals( '<?xml version="1.0" encoding="UTF-8"?> ' . file_get_contents( $file ), $store->get( $attachment_id, false ) );
	}

}
