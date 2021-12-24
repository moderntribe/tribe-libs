<?php declare(strict_types=1);

namespace Tribe\Libs\Container;

use Psr\Container\ContainerInterface;
use ReflectionObject;

/**
 * A scoped container wrapper.
 */
class Container extends \DI\Container implements ScopedContainer {

	/**
	 * A reflection of the wrapper container.
	 *
	 * @var ReflectionObject|null
	 */
	protected $reflectionContainer = null;

	/**
	 * Wrap an existing container and create a new instance of this container.
	 *
	 * Items in the wrapped container are still resolved in this container.
	 *
	 * @param  \Psr\Container\ContainerInterface|null  $container
	 *
	 * @return \Tribe\Libs\Container\ScopedContainer|\DI\Container|ContainerInterface|\DI\FactoryInterface|\Invoker\InvokerInterface
	 */
	public function wrap( ?ContainerInterface $container = null ) {
		$this->delegateContainer = $container ?: $this;

		$object = new self( null, null, $container );

		$object->resolvedEntries = $this->resetEntries();

		return $object->setReflectionContainer( $container );
	}

	/**
	 * Make a completely fresh object, including all of its dependencies.
	 *
	 * @template T
	 *
	 * @param  string|class-string<T>  $name        Entry name or a class name.
	 * @param  array                   $parameters  Optional parameters to use to build the entry. Use this to force
	 *                                              specific parameters to specific values. Parameters not defined in this
	 *                                              array will be resolved using the container.
	 *
	 * @throws \DI\DependencyException
	 * @throws \DI\NotFoundException
	 * @throws \ReflectionException
	 *
	 * @return mixed|T
	 */
	public function makeFresh( $name, array $parameters = [] ) {
		return $this->flush()->make( $name, $parameters );
	}

	/**
	 * Flush the container of all bindings and resolved instances in this
	 * container and the wrapped container.
	 *
	 * @throws \ReflectionException
	 *
	 * @return \Tribe\Libs\Container\ScopedContainer|\DI\Container|ContainerInterface|\DI\FactoryInterface|\Invoker\InvokerInterface
	 */
	public function flush() {
		$this->resetEntries();

		if ( is_null( $this->reflectionContainer ) ) {
			return $this;
		}

		$resolvedEntries = $this->reflectionContainer->getProperty( 'resolvedEntries' );
		$resolvedEntries->setAccessible( true );
		$resolvedEntries->setValue( $this->delegateContainer, [] );

		return $this;
	}

	/**
	 * Reset resolved entries to the default.
	 *
	 * @return array<string, mixed>
	 */
	protected function resetEntries(): array {
		return $this->resolvedEntries = [
			ScopedContainer::class => $this,
		];
	}

	/**
	 * Create a reflection object of the wrapped container.
	 *
	 * @param  \Psr\Container\ContainerInterface|null  $container
	 *
	 * @return \Tribe\Libs\Container\ScopedContainer|\DI\Container|ContainerInterface|\DI\FactoryInterface|\Invoker\InvokerInterface
	 */
	protected function setReflectionContainer( ?ContainerInterface $container = null ) {
		$this->reflectionContainer = $container ? new ReflectionObject( $container ) : null;

		return $this;
	}

}
