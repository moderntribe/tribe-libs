<?php declare(strict_types=1);

namespace Tribe\Libs\Tests\Fixtures;

use Tribe\Libs\Field_Models\Field_Model;

class Parent_Array_Model extends Field_Model {

	/**
	 * @var \Tribe\Libs\Tests\Fixtures\Child_Two_Model[]
	 */
	public array $children = [];

}
