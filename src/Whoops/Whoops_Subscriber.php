<?php declare(strict_types=1);

namespace Tribe\Libs\Whoops;

use Tribe\Libs\Container\Abstract_Subscriber;
use Whoops\Run;

class Whoops_Subscriber extends Abstract_Subscriber {

	public function register(): void {
		if ( ! defined( 'WHOOPS_ENABLE' ) || ! WHOOPS_ENABLE ) {
			return;
		}

		add_action( 'init', function (): void {
			$this->container->get( Run::class )->register();
		}, - 10, 0 );
	}

}
