<?php declare(strict_types=1);

namespace Tribe\Libs\Container\Definition\Helper;

use DI\Definition\AutowireDefinition;
use DI\Definition\Definition;
use DI\Definition\Exception\InvalidDefinition;
use DI\Definition\Helper\AutowireDefinitionHelper;
use DI\Definition\ObjectDefinition;
use DI\Definition\ObjectDefinition\MethodInjection;
use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;

/**
 * Helps define how to create a concrete instance based on a provided
 * interface.
 */
class ContextualDefinitionHelper extends AutowireDefinitionHelper {

	public const DEFINITION_CLASS = AutowireDefinition::class;

	/**
	 * The class name.
	 *
	 * @var string|null
	 */
	protected $class;

	/**
	 * The interface => concrete relationship.
	 *
	 * @var array<string, string|callable>
	 */
	protected $contextual = [];

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
	 * Create the definition with the replaced concrete instances.
	 *
	 * @param  string  $entryName
	 *
	 * @throws \DI\Definition\Exception\InvalidDefinition
	 *
	 * @return \DI\Definition\Definition
	 */
	public function getDefinition( string $entryName ): Definition {
		$definition = parent::getDefinition( $entryName );

		if ( ! empty( $this->contextual ) ) {
			$parameters           = $this->replaceParameters( $definition, $this->contextual );
			$constructorInjection = MethodInjection::constructor( $parameters );

			$definition->setConstructorInjection( $constructorInjection );

			// We now perform all the constructor injection, make sure the parent doesn't.
			unset( $this->constructor );
		}

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
			$type = $parameter->getType();

			// Get built in type values from the constructor parameters
			if ( $type instanceof ReflectionNamedType && $type->isBuiltin() ) {
				$replaced[ $index ] = $this->constructor[ $parameter->getName() ];
				continue;
			}

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
