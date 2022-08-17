<?php declare(strict_types=1);

use Composer\InstalledVersions;
use Psr\Container\ContainerInterface;
use Tribe\Libs\WP_Downloader\Commands\File_Copier;
use Tribe\Libs\WP_Downloader\Extractor;

// PHP-DI Container configuration
return [
	'project_root'   => static fn () => InstalledVersions::getRootPackage()['install_path'],
	Extractor::class => DI\autowire()
		->constructorParameter(
			'project_root',
			static fn ( ContainerInterface $c ) => $c->get( 'project_root' )
		),
	File_Copier::class => DI\autowire()
		->constructorParameter(
			'project_root',
			static fn ( ContainerInterface $c ) => $c->get( 'project_root' )
		),
];
