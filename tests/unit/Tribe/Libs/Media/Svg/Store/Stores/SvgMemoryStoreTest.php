<?php declare(strict_types=1);

namespace Tribe\Libs\Media\Svg\Store\Stores;

use DI\ContainerBuilder;
use enshrined\svgSanitize\Sanitizer;
use Tribe\Libs\Media\Svg\Store\Svg_Parser_Factory;
use Tribe\Libs\Tests\Unit;

final class SvgMemoryStoreTest extends Unit {

	private Svg_Memory_Store $store;
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

		$this->file = codecept_data_dir( 'media/test.svg' );
	}

	public function test_it_saves_and_retrieves_an_svg(): void {
		$id = 55;

		$this->assertTrue( $this->store->save( $this->file, $id ) );
		$this->assertStringEqualsFile( $this->file, $this->store->get( $id ) );
	}

	public function test_it_saves_and_retrieves_an_svg_while_keeping_xml_tag(): void {
		$id = 55;

		$this->assertTrue( $this->store->save( $this->file, $id ) );
		$this->assertEquals( '<?xml version="1.0" encoding="UTF-8"?> ' . file_get_contents( $this->file ), $this->store->get( $id, false ) );
	}

}
