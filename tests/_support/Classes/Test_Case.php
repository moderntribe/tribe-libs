<?php declare(strict_types=1);

namespace Tribe\Libs\Tests;

use Codeception\TestCase\WPTestCase;
use Faker\Factory;
use Faker\Generator;

/**
 * Test case with specific actions for Square One projects.
 *
 * @mixin \Codeception\Test\Unit
 * @mixin \PHPUnit\Framework\TestCase
 *
 * @package Tribe\Libs\Tests
 */
class Test_Case extends WPTestCase {

	protected Generator $faker;

	public function _setUp(): void {
		parent::_setUp();

		$this->faker = Factory::create();
	}

}
