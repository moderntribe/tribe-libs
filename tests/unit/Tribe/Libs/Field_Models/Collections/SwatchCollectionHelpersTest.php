<?php declare(strict_types=1);

namespace Tribe\Libs\Field_Models\Collections;

use Brain\Monkey\Functions;
use Tribe\Libs\Tests\Unit;

class SwatchCollectionHelpersTest extends Unit {

	public function test_format_helpers_and_lookup(): void {
		Functions\when( 'esc_attr' )->returnArg();
		Functions\when( 'esc_html' )->returnArg();

		$collection = Swatch_Collection::create( [
			'black' => [
				'name'  => 'Black',
				'slug'  => 'black',
				'color' => '#000000',
			],
			'white' => [
				'name'  => 'White',
				'slug'  => 'white',
				'color' => '#FFFFFF',
			],
		] );

		$this->assertSame( '#000000', $collection->get_by_value( '#000000' )->color );
		$this->assertNull( $collection->get_by_value( '#123456' ) );

		$acf = $collection->format_for_acf();
		$this->assertSame( 'Black', $acf['#000000'] );
		$this->assertSame( 'White', $acf['#FFFFFF'] );

		$subset = $collection->get_subset( [ 'black' ] );
		$this->assertCount( 1, $subset );
		$this->assertArrayHasKey( 'black', $subset->toArray() );

		$blocks = $collection->format_for_blocks();
		$this->assertSame( 'Black', $blocks[0]['name'] );
		$this->assertSame( 'black', $blocks[0]['slug'] );
	}

}
