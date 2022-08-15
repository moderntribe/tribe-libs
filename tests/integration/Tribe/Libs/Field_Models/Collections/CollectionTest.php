<?php declare(strict_types=1);

namespace Tribe\Libs\Field_Models\Collections;

use Tribe\Libs\Tests\Test_Case;

final class CollectionTest extends Test_Case {

	public function test_user_collection(): void {
		$user_ids = [
			$this->factory()->user->create(),
			$this->factory()->user->create(),
			$this->factory()->user->create(),
			$this->factory()->user->create(),
			$this->factory()->user->create(),
		];

		$post_id = $this->factory()->post->create( [
			'post_status' => 'publish',
		] );

		$field_key = 'field_test_users';

		acf_add_local_field( [
			'key'           => $field_key,
			'name'          => 'test_users',
			'multiple'      => true,
			'type'          => 'user',
			'return_format' => 'array',
		] );

		$this->assertNotEmpty( update_field( $field_key, $user_ids, $post_id ) );

		$collection = User_Collection::create( get_field( $field_key, $post_id ) );

		$this->assertSame( count( $user_ids ), $collection->count() );

		foreach ( $collection as $key => $user ) {
			$wp_user = get_user_by( 'ID', $user_ids[ $key ] );

			$this->assertSame( $wp_user->ID, $user->ID );
			$this->assertSame( $wp_user->user_firstname, $user->user_firstname );
			$this->assertSame( $wp_user->user_lastname, $user->user_lastname );
			$this->assertSame( $wp_user->nickname, $user->nickname );
			$this->assertSame( $wp_user->display_name, $user->display_name );
			$this->assertSame( $wp_user->user_email, $user->user_email );
		}

	}

	public function test_swatch_collection(): void {
		$swatches = [
			'white' => [
				'color' => '#ffffff',
				'name'  => __( 'White', 'tribe' ),
				'slug'  => 'white',
			],
			'black' => [
				'color' => '#000000',
				'name'  => __( 'Black', 'tribe' ),
				'slug'  => 'black',
			],
			'grey'  => [
				'color' => '#696969',
				'name'  => __( 'Grey', 'tribe' ),
				'slug'  => 'grey',
			],
		];

		$collection = Swatch_Collection::create( $swatches );

		// Block formatting is exactly as created, but using integers as keys and the `name` key is escaped.
		$this->assertEquals( [
			[
				'color' => '#ffffff',
				'name'  => 'White',
				'slug'  => 'white',
			],
			[
				'color' => '#000000',
				'name'  => 'Black',
				'slug'  => 'black',
			],
			[
				'color' => '#696969',
				'name'  => 'Grey',
				'slug'  => 'grey',
			],
		], $collection->format_for_blocks() );

		$this->assertEquals( [
			'color' => '#000000',
			'slug'  => 'black',
			'name'  => 'Black',
		], $collection->get_by_value( '#000000' )->toArray() );

		$this->assertEquals( [
			'color' => '#696969',
			'slug'  => 'grey',
			'name'  => 'Grey',
		], $collection->get_by_value( '#696969' )->toArray() );

		$this->assertEquals( [
			'color' => '#ffffff',
			'slug'  => 'white',
			'name'  => 'White',
		], $collection->get_by_value( '#ffffff' )->toArray() );

		$white = $collection->offsetGet( 'white' );
		$this->assertSame( '#ffffff', $white->color );
		$this->assertSame( 'White', $white->name );
		$this->assertSame( 'white', $white->slug );

		$black = $collection->offsetGet( 'black' );
		$this->assertSame( '#000000', $black->color );
		$this->assertSame( 'Black', $black->name );
		$this->assertSame( 'black', $black->slug );

		$grey = $collection->offsetGet( 'grey' );
		$this->assertSame( '#696969', $grey->color );
		$this->assertSame( 'Grey', $grey->name );
		$this->assertSame( 'grey', $grey->slug );

		$subset_collection = $collection->get_subset( [
			'grey',
			'white',
		] );

		$this->assertSame( 2, $subset_collection->count() );
		$this->assertSame( 'white', $subset_collection->get_by_value( '#ffffff' )->slug );
		$this->assertSame( 'grey', $subset_collection->get_by_value( '#696969' )->slug );
		$this->assertNull( $subset_collection->get_by_value( '#000000' ) );

	}

}
