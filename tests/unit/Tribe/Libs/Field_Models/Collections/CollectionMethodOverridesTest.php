<?php declare(strict_types=1);

namespace Tribe\Libs\Field_Models\Collections;

use Tribe\Libs\Field_Models\Models\Image;
use Tribe\Libs\Field_Models\Models\Swatch;
use Tribe\Libs\Field_Models\Models\User;
use Tribe\Libs\Tests\Unit;

class CollectionMethodOverridesTest extends Unit {
	public function test_user_collection_method_overrides(): void {
		$empty_collection = User_Collection::create( [] );

		$this->assertInstanceOf( User_Collection::class, $empty_collection );
		$this->assertNull( $empty_collection->offsetGet( 0 ) );

		$collection = User_Collection::create( [
			[
				'ID' => 1,
			],
		] );

		$this->assertInstanceOf( User_Collection::class, $collection );
		$this->assertInstanceOf( User::class, $collection->current() );
		$this->assertInstanceOf( User::class, $collection->offsetGet( 0 ) );
	}

	public function test_swatch_collection_method_overrides(): void {
		$empty_collection = Swatch_Collection::create( [] );

		$this->assertInstanceOf( Swatch_Collection::class, $empty_collection );
		$this->assertNull( $empty_collection->offsetGet( 0 ) );

		$collection = Swatch_Collection::create( [
			[
				'name' => 'Test',
			],
		] );

		$this->assertInstanceOf( Swatch_Collection::class, $collection );
		$this->assertInstanceOf( Swatch::class, $collection->current() );
		$this->assertInstanceOf( Swatch::class, $collection->offsetGet( 0 ) );
	}

	public function test_gallery_collection_method_overrides(): void {
		$empty_collection = Gallery_Collection::create( [] );

		$this->assertInstanceOf( Gallery_Collection::class, $empty_collection );
		$this->assertNull( $empty_collection->offsetGet( 0 ) );

		$collection = Gallery_Collection::create( [
			[
				'id' => 1,
			],
		] );

		$this->assertInstanceOf( Gallery_Collection::class, $collection );
		$this->assertInstanceOf( Image::class, $collection->current() );
		$this->assertInstanceOf( Image::class, $collection->offsetGet( 0 ) );
	}
}
