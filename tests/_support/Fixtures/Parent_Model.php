<?php declare(strict_types=1);

namespace Tribe\Libs\Tests\Fixtures;

use Tribe\Libs\Field_Models\Field_Model;

class Parent_Model extends Field_Model {

	public Child_One_Model $child_one;
	public string $name = '';

}
