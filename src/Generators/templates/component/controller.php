<?php
declare( strict_types=1 );

namespace %2$s;

use Tribe\Project\Templates\Abstract_Controller;
use Tribe\Project\Templates\Component_Factory;
use %3$s\%1$s as %4$s;

class %1$s extends Abstract_Controller {

	public function render( string $path = '' ): string {
		return $this->factory->get( %4$s::class, [
			%5$s
		] )->render( $path );
	}

}
