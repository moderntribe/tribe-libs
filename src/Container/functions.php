<?php declare(strict_types=1);

namespace Tribe\Libs\Container;

use Tribe\Libs\Container\Definition\Helper\ContextualDefinitionHelper;

/**
 * Extend the PHP-DI container to allow for contextual binding.
 *
 * @example Tribe\Libs\Container\autowire()->contextualParameter( Interface::class, Concrete::class );
 * @example Tribe\Libs\Container\autowire()->contextualParameter( Interface::class, static fn () => new Concrete() );
 *
 * @param  string|null  $className
 *
 * @return \Tribe\Libs\Container\Definition\Helper\ContextualDefinitionHelper
 */
function autowire( ?string $className = null ): ContextualDefinitionHelper {
	return new ContextualDefinitionHelper( $className );
}
