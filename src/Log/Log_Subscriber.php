<?php declare( strict_types=1 );

namespace Tribe\Libs\Cache;

use Tribe\Libs\Container\Abstract_Subscriber;
use Tribe\Libs\Log\Log_Actions;

class Log_Subscriber extends Abstract_Subscriber {

	public function register(): void {
		$this->log();
	}

	protected function log(): void {
		// Set logger action hooks
		add_action( 'init', static function () {
			$this->container->get( Log_Actions::class )->init();
		}, 0, 0 );
	}

}
