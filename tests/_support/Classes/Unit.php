<?php declare(strict_types=1);

namespace Tribe\Libs\Tests;

use Brain\Monkey;

/**
 * Extend this for your unit tests that contain Brain Monkey
 * configuration out of the box.
 *
 * @link https://brain-wp.github.io/BrainMonkey/
 */
class Unit extends \Codeception\Test\Unit {

	use With_DI_Container;

	protected function setUp(): void {
		parent::setUp();
		Monkey\setUp();

		$this->init_builder();
	}

	protected function tearDown(): void {
		Monkey\tearDown();
		parent::tearDown();

		$this->c = null;
	}

}
