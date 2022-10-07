<?php declare(strict_types=1);

namespace Tribe\Libs\Tests;

use Codeception\TestCase\WPTestCase;
use DI\ContainerBuilder;
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

	/**
	 * The PHP-DI container.
	 *
	 * @var \DI\FactoryInterface|\Invoker\InvokerInterface|\Psr\Container\ContainerInterface|null
	 */
	protected $c = null;

	protected function setUp(): void {
		// @phpstan-ignore-next-line
		parent::setUp();

		$this->faker = Factory::create();
	}

	protected function tearDown(): void {
		// @phpstan-ignore-next-line
		parent::tearDown();

		$this->c = null;
	}

	/**
	 * Create a PHP-DI container similar to SquareOne, based on the specific
	 * definers and subscribers required for your test.
	 *
	 * Overload the setUp() method in your test and call this method after.
	 *
	 * @param  array<class-string<\Tribe\Libs\Container\Definer_Interface>>    $definers
	 * @param  array<class-string<\Tribe\Libs\Container\Abstract_Subscriber>>  $subscribers
	 *
	 * @throws \Exception
	 */
	protected function make_di_container( array $definers, array $subscribers = [] ): void {
		$builder = new ContainerBuilder();
		$builder->useAutowiring( true );
		$builder->useAnnotations( false );
		$builder->addDefinitions( ...array_map( static fn( $classname ) => (new $classname())->define(), $definers ) );

		$this->c = $builder->build();

		if ( $subscribers ) {
			foreach ( $subscribers as $subscriber_class ) {
				(new $subscriber_class( $this->c ))->register();
			}
		}
	}

}
