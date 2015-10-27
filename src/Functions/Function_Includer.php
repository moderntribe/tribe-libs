<?php


namespace Tribe\Libs\Functions;


abstract class Function_Includer {
	public static function cache() {
		require_once( __DIR__ . '/cache.php' );
	}

	public static function version() {
		require_once( __DIR__ . '/version.php' );
	}

	public static function cmb2() {
		require_once( __DIR__ . '/cmb2.php' );
	}
}