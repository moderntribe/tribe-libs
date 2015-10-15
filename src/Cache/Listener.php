<?php

namespace Tribe\Libs\Cache;

/**
 * Tracks WP events and expires select caches as necessary
 */
abstract class Listener {
	/** @var Cache */
	protected $cache = null;

	public function __construct() {
		$this->cache = new Cache();
	}

	/**
	 * Register each hook that should lead to something expiring
	 *
	 * @return void
	 */
	abstract public function hook();
}

