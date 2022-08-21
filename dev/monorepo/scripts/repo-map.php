#!/usr/bin/env php
<?php declare(strict_types=1);
/**
 * Build an array map of the sub-repos name and directory relative to the "src/" dir
 * for use in GitHub Actions.
 */

require_once __DIR__ . '/../../../vendor/autoload.php';

$finder = new Symfony\Component\Finder\Finder();
$finder->in( __DIR__ . '/../../../src' )->name( 'composer.json' );

$packages = [];

foreach ( $finder as $file ) {
	$composer = json_decode( $file->getContents() );
	$paths    = explode( DIRECTORY_SEPARATOR, (string) $file->getPathInfo() );;

	$packages[] = [
		'name'      => str_replace( 'moderntribe/', '', $composer->name ),
		'directory' => last( $paths ),
	];
}

echo json_encode( $packages );
