<?php declare(strict_types=1);

namespace Tribe\Libs\Tests;

use DI\ContainerBuilder;

/**
 * Allow test suites to set up a PHP-DI container similar to SquareOne.
 */
trait With_DI_Container {

	/**
	 * The PHP-DI container.
	 *
	 * @var \DI\FactoryInterface|\Invoker\InvokerInterface|\Psr\Container\ContainerInterface|null
	 */
	protected $c = null;
	protected ContainerBuilder $builder;

	/**
	 * Creates a PHP-DI container similar to SquareOne, passing the specific
	 * definers and subscribers required for your use in your specific test.
	 *
	 * Overload the setUp() method in your test and call this method after.
	 *
	 * @param  array<class-string<\Tribe\Libs\Container\Definer_Interface>>    $definers
	 * @param  array<class-string<\Tribe\Libs\Container\Abstract_Subscriber>>  $subscribers
	 *
	 * @throws \Exception
	 */
	protected function init_container( array $definers, array $subscribers = [] ): void {
		$this->builder->addDefinitions( ...array_map( static fn( $classname ) => (new $classname())->define(), $definers ) );

		$this->c = $this->builder->build();

		if ( $subscribers ) {
			foreach ( $subscribers as $subscriber_class ) {
				(new $subscriber_class( $this->c ))->register();
			}
		}
	}

	private function init_builder(): void {
		$builder = new ContainerBuilder();
		$builder->useAutowiring( true );
		$builder->useAnnotations( false );

		$this->builder = $builder;
	}

}
