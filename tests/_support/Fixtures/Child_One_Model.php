<?php declare(strict_types=1);

namespace Tribe\Libs\Tests\Fixtures;

use Tribe\Libs\Field_Models\Field_Model;

class Child_One_Model extends Field_Model {

	public Child_Two_Model $child_two;
	public string $name = '';

}
