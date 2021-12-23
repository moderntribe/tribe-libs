<?php declare(strict_types=1);

namespace Tribe\Libs\Container;

use DI\FactoryInterface;
use Invoker\InvokerInterface;
use Psr\Container\ContainerInterface;

class Container extends \DI\Container implements StatefulContainer {

	/**
	 * Refresh the container state.
	 *
	 * @return ContainerInterface|FactoryInterface|InvokerInterface
	 */
	public function refresh() {
		$this->resolvedEntries = [];

		return $this;
	}

}
