<?php declare(strict_types=1);

namespace Tribe\Libs\Container\Definition;

use DI\Definition\AutowireDefinition;

/**
 * Defines how concrete classes will be instantiated based
 * on their interface.
 */
class ContextualDefinition extends AutowireDefinition {

	/**
	 * The interface => concrete relationship.
	 *
	 * @var array<string, string|callable>
	 */
	protected $contextual;

	/**
	 * Set the definition binding.
	 *
	 * @param  array<string, string|callable>  $contextual
	 *
	 * @return void
	 */
	public function setContextualBinding( array $contextual ) {
		$this->contextual = $contextual;
	}

}
