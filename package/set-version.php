<?php

namespace Tribe\Libs\Package;


if ( empty( $_SERVER['argv'][1] ) ) {
	printf( "Usage: %s <version>\n", $_SERVER['argv'][0] );
	exit( 1 );
}

$version = sanitize_version( $_SERVER['argv'][1] );

if ( empty( $version ) ) {
	echo( "Invalid version given. Use format 'X.Y'." );
	exit( 1 );
}

foreach ( array_keys( get_map() ) as $dir ) {
	set_version( $dir, $version );
}

echo "Complete!\n";

function sanitize_version( $version ): string {
	if ( ! preg_match( '/^\d+\.\d+$/', $version ) ) {
		return '';
	}

	return $version;
}

function get_map(): array {
	return json_decode( file_get_contents( __DIR__ . '/directory-map.json' ), true );
}

function set_version( $dir, $version ) {
	$file_path = get_composer_json_path( $dir );
	$package   = json_decode( file_get_contents( $file_path ), false, 512, JSON_THROW_ON_ERROR );

	if ( ! isset( $package->extra->{'branch-alias'}->{'dev-master'} ) ) {
		echo "Missing \"dev-master\" branch-alias in composer.json extra.\n";
		exit( 1 );
	}

	$package->extra->{'branch-alias'}->{'dev-master'} = sprintf( '%s-dev', $version );

	file_put_contents( $file_path, json_encode( $package, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR ) );

	printf( "Set %s version to %s\n", $dir, $version );
}

function get_composer_json_path( $dir ): string {
	$file_path = sprintf( '%s/%s/composer.json', dirname( __DIR__ ), rtrim( $dir, '/' ) );
	if ( ! file_exists( $file_path ) ) {
		throw new File_Not_Found_Exception( sprintf( 'Unable to load composer file for path %s', $file_path ) );
	}

	return $file_path;
}

class File_Not_Found_Exception extends \RuntimeException {
}