<?php declare(strict_types=1);

namespace Tribe\Libs\Tests\Fixtures;

use Tribe\Libs\ACF\Contracts\Has_Sub_Fields;
use Tribe\Libs\ACF\Traits\With_Field_Finder;

/**
 * Fixture to test the field finder trait.
 */
class FieldFinder {

	use With_Field_Finder;

	/**
	 * @var \Tribe\Libs\ACF\Field[]
	 */
	private $fields;

	public function __construct( array $fields ) {
		$this->fields = $fields;
	}

	public function find( string $key ): ?Has_Sub_Fields {
		return $this->find_field( $this->fields, $key );
	}

}
