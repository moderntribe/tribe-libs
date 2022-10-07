<?php declare(strict_types=1);

namespace Tribe\Libs\Media\Svg\Store;

use DI\ContainerBuilder;
use enshrined\svgSanitize\Sanitizer;
use Mockery;
use RuntimeException;
use Brain\Monkey\Functions;
use Tribe\Libs\Media\Svg\Store\Stores\Svg_Memory_Store;
use Tribe\Libs\Tests\Unit;

final class SvgStoreHandlerTest extends Unit {

	private Svg_Memory_Store $store;
	protected Svg_Store_Handler $handler;
	private string $file;

	protected function setUp(): void {
		parent::setUp();

		$sanitizer = new Sanitizer();
		$sanitizer->removeXMLTag( true );
		$sanitizer->minify( true );
		$sanitizer->setXMLOptions( 0 );

		$this->store = new Svg_Memory_Store(
			new Svg_Parser_Factory( ( new ContainerBuilder() )->build() ),
			$sanitizer
		);

		$this->file    = codecept_data_dir( 'media/test.svg' );
		$this->handler = new Svg_Store_Handler( $this->store );
	}

	public function test_handler_allows_svg_store(): void {
		$attachment_id = 1;

		Functions\when( 'get_post_mime_type' )->justReturn( 'image/svg+xml' );

		$file = $this->handler->store( $this->file, $attachment_id );

		$this->assertSame( $file, $this->file );
		$this->assertSame( $this->store->get( $attachment_id ), file_get_contents( $this->file ) );
	}

	public function test_handler_bypass_non_svg_files(): void {
		$attachment_id = 2;

		Functions\when( 'get_post_mime_type' )->justReturn( 'image/gif' );

		$this->handler->store( codecept_data_dir( 'media/test.gif' ), $attachment_id );

		$this->assertEmpty( $this->store->get( $attachment_id ) );
	}

	public function test_handler_throws_exception_when_store_fails_to_save_svg_markup(): void {
		$this->expectException( RuntimeException::class );
		$attachment_id = 3;

		Functions\when( 'get_post_mime_type' )->justReturn( 'image/svg+xml' );

		$store = Mockery::mock( Svg_Memory_Store::class );
		$store->shouldReceive( 'save' )
			  ->once()
			  ->with( $this->file, $attachment_id )
			  ->andReturnFalse();

		$handler = new Svg_Store_Handler( $store );

		$handler->store( $this->file, $attachment_id );
	}

}
