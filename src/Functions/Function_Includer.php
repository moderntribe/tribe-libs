<?php


namespace Tribe\Libs\Functions;


abstract class Function_Includer {

	public static function cache() {
		require_once( __DIR__ . '/cache.php' );
	}

	public static function version() {
		require_once( __DIR__ . '/version.php' );
	}

	public static function project() {
		$files = apply_filters( 'tribe/core/Functions_Includer/project_files', array() );
		if ( empty( $files ) ) {
			return;
		}
		foreach ( $files as $file ) {
			require_once( $file );
		}
	}
}