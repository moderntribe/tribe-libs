<?php

namespace Tribe\Libs\Package;


if ( empty( $_SERVER['argv'][1] ) ) {
	printf( "Usage: %s <tag>\n", $_SERVER['argv'][0] );
	exit( 1 );
}

$tag = sanitize_version( $_SERVER['argv'][1] );

$remote        = 'origin';
$repo_owner    = 'moderntribe';
$upstream_repo = sprintf( 'git@github.com:%s/%s.git', $repo_owner, 'tribe-libs' );
$branches      = [ 'feature/monorepo' ]; // [ 'master' ];

chdir( __DIR__ );
passthru( 'docker build -t tribe-libs-package:latest .', $exit );
if ( $exit ) {
	exit( $exit );
}

if ( $tag && ! tag_exists( $tag, $remote ) ) {
	create_tag( $tag, $remote );
}

if ( ! is_dir( __DIR__ . '/.subsplit' ) ) {
	docker_run( sprintf(
		'git subsplit init "%s"',
		$upstream_repo
	) );
}

docker_run( sprintf(
	'git subsplit update --heads="%s" --tags="%s"',
	implode( ' ', $branches ),
	$tag
) );

docker_run( sprintf(
	'git subsplit publish "%s" --heads="%s" --tags="%s"',
	implode( ' ', repo_list( $repo_owner ) ),
	implode( ' ', $branches ),
	$tag
) );

echo "Complete!\n";

function sanitize_version( $version ): string {
	if ( ! preg_match( '/^\d+\.\d+\.\d+$/', $version ) ) {
		return '';
	}

	return $version;
}

function get_map(): array {
	return json_decode( file_get_contents( __DIR__ . '/directory-map.json' ), true );
}

function repo_list( $repo_owner ): array {
	$list = [];
	foreach ( get_map() as $dir => $repo_suffix ) {
		$list[] = sprintf( '%s:git@github.com:%s/%s.git', $dir, $repo_owner, $repo_suffix );
	}

	return $list;
}

function docker_run( $cmd ) {
	$command = sprintf(
		'docker run --rm -v "%s":/project -v ~/.ssh:/root/.ssh -w "/project"  tribe-libs-package:latest bash -c \'( %s )\'',
		__DIR__,
		$cmd
	);
	passthru( $command, $exit );
	if ( $exit ) {
		exit( $exit );
	}
}

function tag_exists( $tag, $remote ) {
	$cmd = sprintf( 'git ls-remote --tags --exit-code %s refs/tags/%s > /dev/null', $remote, $tag );
	passthru( $cmd, $exit );

	return ! $exit;
}

function create_tag( $tag, $remote ) {
	passthru( sprintf( 'git tag %s', $tag ), $exit );
	if ( $exit ) {
		exit( $exit );
	}
	passthru( sprintf( 'git push %s %s', $remote, $tag ), $exit );
	if ( $exit ) {
		exit( $exit );
	}
}