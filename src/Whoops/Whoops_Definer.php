<?php declare(strict_types=1);

namespace Tribe\Libs\Whoops;

use DI;
use Tribe\Libs\Container\Definer_Interface;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;

class Whoops_Definer implements Definer_Interface {

	public function define(): array {
		return [
			Run::class => DI\autowire()
				->constructor( null )
				->method( 'pushHandler', DI\get( PrettyPageHandler::class ) ),
		];
	}

}
