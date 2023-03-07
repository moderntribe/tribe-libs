<?php declare(strict_types=1);

namespace Tribe\Libs\Pipeline;

use DI;
use Tribe\Libs\Container\Definer_Interface;
use Tribe\Libs\Pipeline\Contracts\Pipeline as PipelineContract;

class Pipeline_Definer implements Definer_Interface {

	public function define(): array {
		return [
			PipelineContract::class => DI\autowire( Pipeline::class ),
		];
	}

}
