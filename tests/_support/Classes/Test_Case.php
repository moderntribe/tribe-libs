<?php declare(strict_types=1);

namespace Tribe\Libs\Tests;

use Codeception\TestCase\WPTestCase;
use Faker\Factory;
use Faker\Generator;

/**
 * Test case with specific actions for Square One projects.
 *
 * @mixin \PHPUnit\Framework\TestCase
 * @mixin \Codeception\Test\Unit
 * @mixin \Codeception\PHPUnit\TestCase
 *
 * @package Tribe\Libs\Tests
 */
class Test_Case extends WPTestCase {

	protected Generator $faker;

	protected function setUp(): void {
		// @phpstan-ignore-next-line
		parent::setUp();

		$this->faker = Factory::create();
	}

}
