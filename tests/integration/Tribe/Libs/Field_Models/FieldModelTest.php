<?php declare(strict_types=1);

namespace Tribe\Libs\Field_Models;

use Tribe\Libs\Tests\Test_Case;

final class FieldModelTest extends Test_Case {

	public function test_it_casts_values_based_on_defined_type(): void {
		$test_field = new class extends Field_Model {

			public int $id = 0;
			public string $message = '';
			public array $data = [];
			public array $parsedData = [];

		};

		// Passing invalid types, besides $parsedData.
		$data = [
			'id'      => '1',
			'message' => false,
			'data'    => 'hey',
			'parsedData' => [
				0 => 'test',
				1 => 'test two',
			],
		];

		$model      = new $test_field( $data );
		$model_data = $model->toArray();

		// It should utilize the defaults as defined in the model,
		// besides parsedData that was passed correctly.
		$this->assertSame( [
			'id'      => 1,
			'message' => '',
			'data'    => [],
			'parsedData' => [
				0 => 'test',
				1 => 'test two',
			],
		], $model_data );
	}

}
