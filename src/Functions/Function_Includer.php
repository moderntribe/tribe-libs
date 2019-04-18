<?php


namespace Tribe\Libs\Functions;


abstract class Function_Includer {

	public static function version() {
		require_once( __DIR__ . '/version.php' );
	}
}