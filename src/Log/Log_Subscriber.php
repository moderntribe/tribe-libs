<?php declare(strict_types=1);

namespace Tribe\Libs\Log;

use Tribe\Libs\Container\Abstract_Subscriber;

class Log_Subscriber extends Abstract_Subscriber {

	public function register(): void {
		$this->log();
	}

	private function log(): void {
		// Initialize logger action hooks
		add_action( 'init', function () {
			$this->container->get( Log_Actions::class )->init();
		}, 0, 0 );
	}

}
