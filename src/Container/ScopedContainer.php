<?php declare(strict_types=1);

namespace Tribe\Libs\Container;

use Psr\Container\ContainerInterface;

interface ScopedContainer {

	/**
	 * Wrap an existing container and create a new instance of this container.
	 *
	 * Items in the wrapped container are still resolved in this container.
	 *
	 * @param  \Psr\Container\ContainerInterface|null  $container
	 *
	 * @return \Tribe\Libs\Container\ScopedContainer|\DI\Container|ContainerInterface|\DI\FactoryInterface|\Invoker\InvokerInterface
	 */
	public function wrap( ?ContainerInterface $container = null );

	/**
	 * Make a completely fresh object, including its dependencies.
	 *
	 * @template T
	 *
	 * @param  string|class-string<T>  $name        Entry name or a class name.
	 * @param  array                   $parameters  Optional parameters to use to build the entry. Use this to force
	 *                                              specific parameters to specific values. Parameters not defined in this
	 *                                              array will be resolved using the container.
	 *
	 * @return mixed|T
	 */
	public function makeFresh( string $name, array $parameters = [] );

	/**
	 * Flush the container of all bindings and resolved instances in this
	 * container and the wrapped container.
	 *
	 * @return \Tribe\Libs\Container\ScopedContainer|\DI\Container|ContainerInterface|\DI\FactoryInterface|\Invoker\InvokerInterface
	 */
	public function flush();

}
