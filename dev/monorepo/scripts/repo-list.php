#!/usr/bin/env php
<?php declare(strict_types=1);
/**
 * Used in the sub-repo-branch-create.yml GitHub Workflow.
 */

// Parse the root composer.json
$composer = json_decode( file_get_contents( realpath( __DIR__ . '/../../../composer.json' ) ) );

// Remove the organization from the repo.
$repos = array_map(
	static fn( string $repo ) => str_replace( 'moderntribe/', '', $repo ),
	array_keys( (array) $composer->replace )
);

// Display a json array of sub-repo names, e.g. square1-acf.
echo json_encode( $repos );
