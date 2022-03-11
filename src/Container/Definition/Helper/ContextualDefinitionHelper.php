<?php declare(strict_types=1);

namespace Tribe\Libs\Container\Definition\Helper;

use DI\Definition\AutowireDefinition;
use DI\Definition\Definition;
use DI\Definition\Exception\InvalidDefinition;
use DI\Definition\Helper\DefinitionHelper;
use DI\Definition\ObjectDefinition;
use DI\Definition\ObjectDefinition\MethodInjection;
use ReflectionClass;
use ReflectionException;
use Tribe\Libs\Container\Definition\ContextualDefinition;

/**
 * Helps define how to create a concrete instance based on a provided
 * interface.
 */
class ContextualDefinitionHelper implements DefinitionHelper {

	/**
	 * @var string|null
	 */
	protected $className;

	/**
	 * The interface => concrete relationship.
	 *
	 * @var array<string, string|callable>
	 */
	protected $contextual = [];

	/**
	 * @param  string|null  $className  If null, will automatically use the FQCN in the container.
	 */
	public function __construct( ?string $className = null ) {
		$this->className = $className;
	}

	/**
	 * Map a concrete class/instance to an interface/abstract or even another concrete class.
	 *
	 * @param  string                    $interface  The fully qualified class name or interface.
	 * @param  string|\Closure|callable  $concrete   The concrete fully qualified class name or closure that returns an instance.
	 *
	 * @return $this
	 */
	public function contextualParameter( string $interface, $concrete ): self {
		$this->contextual[ $interface ] = $concrete;

		return $this;
	}

	/**
	 * Create the definition with the replaced concretes.
	 *
	 * @param  string  $entryName
	 *
	 * @throws \DI\Definition\Exception\InvalidDefinition
	 *
	 * @return \DI\Definition\Definition
	 */
	public function getDefinition( string $entryName ): Definition {
		$definition = new ContextualDefinition( $entryName, $this->className );
		$definition->setContextualBinding( $this->contextual );

		$parameters           = $this->replaceParameters( $definition, $this->contextual );
		$constructorInjection = MethodInjection::constructor( $parameters );

		$definition->setConstructorInjection( $constructorInjection );

		return $definition;
	}

	/**
	 * Replace interfaces/abstract constructor parameters with their concrete implementation.
	 *
	 * @throws \DI\Definition\Exception\InvalidDefinition
	 */
	protected function replaceParameters( ObjectDefinition $definition, array $parameters ): array {
		$replaced = [];

		try {
			$constructorParameters = ( new ReflectionClass( $definition->getClassName() ) )
				->getConstructor()
				->getParameters();
		} catch ( ReflectionException $e ) {
			throw InvalidDefinition::create(
				$definition,
				sprintf( 'Could not get constructor ReflectionParameters from %s', $definition->getClassName() )
			);
		}

		// Map constructor parameters to their numbered index. The order must match what's in the constructor.
		foreach ( $constructorParameters as $index => $parameter ) {
			$interface = $parameter->getClass()->getName();

			// Keep existing parameters if they haven't been specifically defined.
			if ( ! isset( $parameters[ $interface ] ) ) {
				$replaced[ $index ] = $parameter;
				continue;
			}

			// Create the definition based on the type provided, possibly replacing the interface with its concrete.
			$replaced[ $index ] = $this->createDefinition( $interface, $parameters[ $interface ] );
		}

		return $replaced;
	}

	/**
	 * @param  string           $interface
	 * @param  string|callable  $concrete
	 *
	 * @return callable|\DI\Definition\AutowireDefinition
	 */
	protected function createDefinition( string $interface, $concrete ) {
		return is_callable( $concrete ) ? $concrete : new AutowireDefinition( $interface, $concrete );
	}

}
