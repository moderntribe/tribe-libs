<?php declare(strict_types=1);

namespace Tribe\Libs\Tests\Fixtures;

use Tribe\Libs\Field_Models\Field_Model;

class Parent_Array_Multi_Level_Model extends Field_Model {

	/**
	 * @var \Tribe\Libs\Tests\Fixtures\Parent_Array_Model[]
	 */
	public array $parents = [];

	/**
	 * @var \Tribe\Libs\Tests\Fixtures\Title_Model[]
	 */
	public array $titles = [];

}
