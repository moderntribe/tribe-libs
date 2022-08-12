<?php declare(strict_types=1);

namespace Tribe\Libs\Settings;

use DI;
use Tribe\Libs\Container\Definer_Interface;

class Settings_Definer implements Definer_Interface {

	public const PAGES = 'libs.settings.pages';

	public function define(): array {
		return [
			/**
			 * The array of settings pages that will be registered.
			 * Add more in other Definers using Settings_Definer::PAGES => DI\add( [ ... ] ).
			 *
			 * Pages should extend \Tribe\Libs\Settings\Base_Settings
			 */
			self::PAGES => DI\add( [] ),
		];
	}

}
