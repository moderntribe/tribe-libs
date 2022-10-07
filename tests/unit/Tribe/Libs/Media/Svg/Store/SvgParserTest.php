<?php declare(strict_types=1);

namespace Tribe\Libs\Media\Svg\Store;

use DOMDocument;
use Tribe\Libs\Tests\Unit;

final class SvgParserTest extends Unit {

	private Svg_Parser $parser;
	private string $file;

	protected function setUp(): void {
		parent::setUp();

		$this->parser = new Svg_Parser( new DOMDocument() );
		$this->file   = codecept_data_dir( 'media/test.svg' );
	}

	public function test_it_loads_svg_file(): void {
		$this->parser->load_file( $this->file );

		$this->assertEquals( file_get_contents( $this->file ), (string) $this->parser );
	}

	public function test_it_loads_svg_strings(): void {
		$svg = file_get_contents( $this->file );

		$this->parser->load_string( $svg );

		$this->assertEquals( file_get_contents( $this->file ), (string) $this->parser );
	}

	public function test_gzipped_svg(): void {
		$svg     = file_get_contents( $this->file );
		$gzipped = gzencode( $svg );

		$this->assertSame( 0, mb_strpos( $gzipped, "\x1f" . "\x8b" . "\x08" ) );

		$this->parser->load_string( $gzipped );

		$this->assertEquals( $svg, (string) $this->parser );
	}

}
