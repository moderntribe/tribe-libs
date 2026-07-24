<?php declare(strict_types=1);

namespace Tribe\Libs\Field_Models\DTO;

use ReflectionProperty;
use Tribe\Libs\Field_Models\Collections\User_Collection;
use Tribe\Libs\Field_Models\Models\User;
use Tribe\Libs\Tests\Unit;

class ValueCasterTest extends Unit {

	public function test_cast_value_hydrates_nested_dto(): void {
		$caster = new Value_Caster();
		$user   = $caster->castValue( [ 'ID' => 9, 'user_firstname' => 'Nine' ], [ User::class ] );

		$this->assertInstanceOf( User::class, $user );
		$this->assertSame( 9, $user->ID );
		$this->assertSame( 'Nine', $user->user_firstname );
	}

	public function test_cast_collection_hydrates_list_of_dtos(): void {
		$caster = new Value_Caster();
		$users  = $caster->castCollection(
			[
				[ 'ID' => 1 ],
				[ 'ID' => 2 ],
			],
			[ User::class ]
		);

		$this->assertCount( 2, $users );
		$this->assertInstanceOf( User::class, $users[0] );
		$this->assertSame( 2, $users[1]->ID );
	}

	public function test_collection_type_detects_collection_class(): void {
		$caster = new Value_Caster();

		$this->assertSame( User_Collection::class, $caster->collectionType( [ User_Collection::class ] ) );
		$this->assertFalse( $caster->collectionType( [ User::class ] ) );
	}

	public function test_should_be_cast_to_collection_requires_list_of_arrays(): void {
		$caster = new Value_Caster();

		$this->assertTrue( $caster->shouldBeCastToCollection( [ [ 'a' => 1 ], [ 'b' => 2 ] ] ) );
		$this->assertFalse( $caster->shouldBeCastToCollection( [] ) );
		$this->assertFalse( $caster->shouldBeCastToCollection( [ 'keyed' => [ 'a' => 1 ] ] ) );
		$this->assertFalse( $caster->shouldBeCastToCollection( [ 'not-array' ] ) );
	}

	public function test_cast_wraps_list_in_collection_type(): void {
		$property = new ReflectionProperty( new class {
			public User_Collection $users;
		}, 'users' );

		$validator = Field_Validator::fromReflection( $property );
		$caster    = new Value_Caster();
		$result    = $caster->cast(
			[
				[ 'ID' => 3, 'user_firstname' => 'Three' ],
			],
			$validator
		);

		$this->assertInstanceOf( User_Collection::class, $result );
		$this->assertCount( 1, $result );
		$this->assertSame( 3, $result[0]->ID );
	}

}
