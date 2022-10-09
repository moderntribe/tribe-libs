<?php declare(strict_types=1);

namespace Tribe\Libs\Media\Svg\Store;

use DOMDocument;
use Tribe\Libs\Tests\Unit;

final class SvgParserFactoryTest extends Unit {

	private Svg_Parser_Factory $factory;

	protected function setUp(): void {
		parent::setUp();

		$this->builder->addDefinitions( [
			DOMDocument::class => new DOMDocument(),
		] );

		$this->factory = new Svg_Parser_Factory( $this->builder->build() );
	}

	public function test_it_makes_a_parser_from_file(): void {
		$file = codecept_data_dir( 'media/test.svg' );

		$parser = $this->factory->make( $file );

		$this->assertStringEqualsFile( $file, (string) $parser );
	}

	public function test_it_makes_a_parser_from_string(): void {
		$svg = '<svg height="100" width="100"><circle cx="50" cy="50" r="40" stroke="black" stroke-width="3" fill="red" /></svg>';

		$parser = $this->factory->make( $svg );

		$this->assertEquals( $svg, (string) $parser );
	}

}
