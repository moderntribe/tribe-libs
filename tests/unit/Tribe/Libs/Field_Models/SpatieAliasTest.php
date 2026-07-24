<?php declare(strict_types=1);

namespace Tribe\Libs\Field_Models;

use Tribe\Libs\Field_Models\DTO\Data_Transfer_Object;
use Tribe\Libs\Field_Models\DTO\Data_Transfer_Object_Collection;
use Tribe\Libs\Field_Models\DTO\Field_Validator;
use Tribe\Libs\Field_Models\DTO\Value_Caster;
use Tribe\Libs\Tests\Unit;

/**
 * Ensures Spatie class aliases remain available for 4.x → 5.x upgrades.
 */
class SpatieAliasTest extends Unit {

	public function test_spatie_aliases_resolve_to_first_party_dto_classes(): void {
		$this->assertTrue( class_exists( \Spatie\DataTransferObject\DataTransferObject::class ) );
		$this->assertTrue( class_exists( \Spatie\DataTransferObject\FlexibleDataTransferObject::class ) );
		$this->assertTrue( class_exists( \Spatie\DataTransferObject\DataTransferObjectCollection::class ) );
		$this->assertTrue( class_exists( \Spatie\DataTransferObject\ValueCaster::class ) );
		$this->assertTrue( class_exists( \Spatie\DataTransferObject\FieldValidator::class ) );

		// ::class on an alias returns the alias string; resolve the real target class.
		$this->assertSame( Data_Transfer_Object::class, ( new \ReflectionClass( \Spatie\DataTransferObject\DataTransferObject::class ) )->getName() );
		$this->assertSame( Data_Transfer_Object::class, ( new \ReflectionClass( \Spatie\DataTransferObject\FlexibleDataTransferObject::class ) )->getName() );
		$this->assertSame( Data_Transfer_Object_Collection::class, ( new \ReflectionClass( \Spatie\DataTransferObject\DataTransferObjectCollection::class ) )->getName() );
		$this->assertSame( Value_Caster::class, ( new \ReflectionClass( \Spatie\DataTransferObject\ValueCaster::class ) )->getName() );
		$this->assertSame( Field_Validator::class, ( new \ReflectionClass( \Spatie\DataTransferObject\FieldValidator::class ) )->getName() );
	}

	public function test_models_can_extend_spatie_flexible_alias(): void {
		$model = new class( [ 'name' => 'alias' ] ) extends \Spatie\DataTransferObject\FlexibleDataTransferObject {
			public string $name = '';
		};

		$this->assertInstanceOf( Data_Transfer_Object::class, $model );
		$this->assertSame( 'alias', $model->name );
	}

}
