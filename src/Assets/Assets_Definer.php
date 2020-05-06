<?php
declare( strict_types=1 );

namespace Tribe\Libs\Assets;

use DI;
use Tribe\Libs\Container\Definer_Interface;

class Assets_Definer implements Definer_Interface {
	public function define(): array {
		return [
			Asset_Loader::class => DI\create()
				->constructor( DI\get( 'plugin.file' ) ),
		];
	}
}
