<?php declare(strict_types=1);

namespace Tribe\Libs\Tests\Fixtures;

use Tribe\Libs\Field_Models\Collections\User_Collection;
use Tribe\Libs\Field_Models\Field_Model;

class Collection_Model extends Field_Model {

	public User_Collection $users;

}
