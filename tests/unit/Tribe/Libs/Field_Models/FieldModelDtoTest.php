<?php declare(strict_types=1);

namespace Tribe\Libs\Field_Models;

use Tribe\Libs\Field_Models\Collections\User_Collection;
use Tribe\Libs\Field_Models\DTO\Field_Validator;
use Tribe\Libs\Field_Models\DTO\Value_Caster;
use Tribe\Libs\Field_Models\Models\Link;
use Tribe\Libs\Field_Models\Models\User;
use Tribe\Libs\Tests\Fixtures\Child_Two_Model;
use Tribe\Libs\Tests\Fixtures\Collection_Model;
use Tribe\Libs\Tests\Fixtures\Parent_Array_Model;
use Tribe\Libs\Tests\Fixtures\Parent_Model;
use Tribe\Libs\Tests\Unit;

/**
 * Coverage for the first-party Field Model DTO layer (Spatie replacement).
 */
class FieldModelDtoTest extends Unit {

	public function test_it_hydrates_scalars_and_ignores_unknown_keys(): void {
		$model = new Link( [
			'title'   => 'Docs',
			'url'     => 'https://example.com',
			'target'  => '_blank',
			'unknown' => 'ignored',
		] );

		$this->assertSame( 'Docs', $model->title );
		$this->assertSame( 'https://example.com', $model->url );
		$this->assertSame( '_blank', $model->target );
		$this->assertArrayNotHasKey( 'unknown', $model->toArray() );
	}

	public function test_it_casts_invalid_scalar_types(): void {
		$model = new class( [
			'id'      => '42',
			'message' => false,
			'data'    => 'nope',
			'flag'    => 1,
		] ) extends Field_Model {
			public int $id = 0;
			public string $message = '';
			public array $data = [];
			public bool $flag = false;
		};

		$this->assertSame( 42, $model->id );
		$this->assertSame( '', $model->message );
		$this->assertSame( [], $model->data );
		$this->assertTrue( $model->flag );
	}

	public function test_it_uses_defaults_for_missing_required_properties(): void {
		$model = new User( [] );

		$this->assertSame( 0, $model->ID );
		$this->assertSame( '', $model->user_email );
	}

	public function test_array_of_creates_model_instances(): void {
		$users = User::arrayOf( [
			[ 'ID' => 1, 'user_firstname' => 'Ada' ],
			[ 'ID' => 2, 'user_firstname' => 'Grace' ],
		] );

		$this->assertCount( 2, $users );
		$this->assertInstanceOf( User::class, $users[0] );
		$this->assertSame( 'Ada', $users[0]->user_firstname );
		$this->assertSame( 'Grace', $users[1]->user_firstname );
	}

	public function test_only_and_except_filter_to_array_output(): void {
		$model = new Link( [
			'title'  => 'Home',
			'url'    => '/',
			'target' => '_self',
		] );

		$this->assertSame( [ 'title' => 'Home' ], $model->only( 'title' )->toArray() );
		$this->assertSame( [
			'title'  => 'Home',
			'target' => '_self',
		], $model->except( 'url' )->toArray() );
	}

	public function test_nested_models_and_empty_nested_defaults(): void {
		$filled = new Parent_Model( [
			'name'      => 'Parent',
			'child_one' => [
				'name'      => 'Child',
				'child_two' => [ 'name' => 'Grandchild' ],
			],
		] );

		$this->assertInstanceOf( Child_Two_Model::class, $filled->child_one->child_two );
		$this->assertSame( 'Grandchild', $filled->child_one->child_two->name );

		$empty = new Parent_Model( [ 'name' => 'Parent only' ] );
		$this->assertSame( 'This is my default', $empty->child_one->child_two->name );
	}

	public function test_docblock_array_of_models_casting(): void {
		$model = new Parent_Array_Model( [
			'children' => [
				[ 'name' => 'A' ],
				[ 'name' => 'B' ],
			],
		] );

		$this->assertCount( 2, $model->children );
		$this->assertInstanceOf( Child_Two_Model::class, $model->children[0] );
		$this->assertSame( 'B', $model->children[1]->name );
	}

	public function test_collection_property_casts_list_and_empty_values(): void {
		$filled = new Collection_Model( [
			'users' => [
				[ 'ID' => 7, 'user_firstname' => 'Seven' ],
			],
		] );

		$this->assertInstanceOf( User_Collection::class, $filled->users );
		$this->assertCount( 1, $filled->users );
		$this->assertSame( 7, $filled->users[0]->ID );

		foreach ( [ [], [ 'users' => false ], [ 'users' => '' ] ] as $payload ) {
			$model = new Collection_Model( $payload );
			$this->assertInstanceOf( User_Collection::class, $model->users );
			$this->assertCount( 0, $model->users );
		}
	}

	public function test_cast_value_override_remains_callable_for_bc(): void {
		$model = new class extends Field_Model {
			public string $label = '';
			public int $cast_hits = 0;

			protected function castValue( Value_Caster $valueCaster, Field_Validator $fieldValidator, $value ) {
				if ( $fieldValidator->allowedTypes === [ 'string' ] || in_array( 'string', $fieldValidator->allowedTypes, true ) ) {
					$this->cast_hits++;
				}

				return parent::castValue( $valueCaster, $fieldValidator, $value );
			}
		};

		$instance = new $model( [ 'label' => 'hello' ] );

		$this->assertSame( 'hello', $instance->label );
		$this->assertGreaterThan( 0, $instance->cast_hits );
	}

}
