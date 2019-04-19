<?php

namespace Tribe\Libs\Queues\Contracts;

interface Task {

	public function handle( array $args ) : bool;
}
