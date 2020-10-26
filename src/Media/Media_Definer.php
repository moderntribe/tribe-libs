<?php
declare( strict_types=1 );

namespace Tribe\Libs\Media;

use enshrined\svgSanitize\Sanitizer;
use Psr\Container\ContainerInterface;
use Tribe\Libs\Container\Definer_Interface;

class Media_Definer implements Definer_Interface {
	public function define(): array {
		return [
			Sanitizer::class => static function ( ContainerInterface $container ) {
				$sanitizer = new Sanitizer();
				$sanitizer->minify( true );

				return $sanitizer;
			},
		];
	}

}
