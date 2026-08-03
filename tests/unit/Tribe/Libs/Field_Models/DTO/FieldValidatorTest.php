<?php declare(strict_types=1);

namespace Tribe\Libs\Field_Models\DTO;

use ReflectionProperty;
use Tribe\Libs\Field_Models\Collections\User_Collection;
use Tribe\Libs\Tests\Unit;

class FieldValidatorTest extends Unit {

	public function test_from_reflection_reads_native_property_types(): void {
		$property = new ReflectionProperty( new class {
			public string $title = '';
			public ?int $count = null;
		}, 'title' );

		$validator = Field_Validator::fromReflection( $property );

		$this->assertTrue( $validator->hasTypeDeclaration );
		$this->assertTrue( $validator->hasDefaultValue );
		$this->assertFalse( $validator->isNullable );
		$this->assertContains( 'string', $validator->allowedTypes );
		$this->assertTrue( $validator->isValidType( 'ok' ) );
		$this->assertFalse( $validator->isValidType( 1 ) );
	}

	public function test_from_reflection_reads_docblock_array_of_models(): void {
		$property = new ReflectionProperty( new class {
			/** @var \Tribe\Libs\Tests\Fixtures\Child_Two_Model[] */
			public array $children = [];
		}, 'children' );

		$validator = Field_Validator::fromReflection( $property );

		$this->assertNotEmpty( $validator->allowedArrayTypes );
		$this->assertStringContainsString( 'Child_Two_Model', $validator->allowedArrayTypes[0] );
	}

	public function test_collection_property_exposes_item_types_from_current_return(): void {
		$property = new ReflectionProperty( new class {
			public User_Collection $users;
		}, 'users' );

		$validator = Field_Validator::fromReflection( $property );

		$this->assertContains( User_Collection::class, $validator->allowedTypes );
		$this->assertNotEmpty( $validator->allowedArrayTypes );
	}

	public function test_nullable_union_allows_null(): void {
		$property = new ReflectionProperty( new class {
			public ?string $optional = null;
		}, 'optional' );

		$validator = Field_Validator::fromReflection( $property );

		$this->assertTrue( $validator->isNullable );
		$this->assertTrue( $validator->isValidType( null ) );
		$this->assertTrue( $validator->isValidType( 'text' ) );
	}

}
