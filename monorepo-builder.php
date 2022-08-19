<?php declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\MonorepoBuilder\ValueObject\Option;

return static function ( ContainerConfigurator $containerConfigurator ): void {
	$parameters = $containerConfigurator->parameters();

	$parameters->set( Option::PACKAGE_DIRECTORIES, [
		__DIR__ . '/src',
	] );

	$parameters->set( Option::DIRECTORIES_TO_REPOSITORIES, [
		'src/ACF'           => "git@github.com:moderntribe/square1-acf.git",
		'src/Assets'        => "git@github.com:moderntribe/square1-assets.git",
		'src/Blog_Copier'   => "git@github.com:moderntribe/square1-blog-copier.git",
		'src/CLI'           => "git@github.com:moderntribe/square1-cli.git",
		'src/Cache'         => "git@github.com:moderntribe/square1-cache.git",
		'src/Container'     => "git@github.com:moderntribe/square1-container.git",
		'src/Field_Models'  => "git@github.com:moderntribe/square1-field-models.git",
		'src/Generators'    => "git@github.com:moderntribe/square1-generators.git",
		'src/Log'           => "git@github.com:moderntribe/square1-log.git",
		'src/Media'         => "git@github.com:moderntribe/square1-media.git",
		'src/Nav'           => "git@github.com:moderntribe/square1-nav.git",
		'src/Object_Meta'   => "git@github.com:moderntribe/square1-object-meta.git",
		'src/Oembed'        => "git@github.com:moderntribe/square1-oembed.git",
		'src/P2P'           => "git@github.com:moderntribe/square1-p2p.git",
		'src/Pipeline'      => "git@github.com:moderntribe/square1-pipeline.git",
		'src/Post_Meta'     => "git@github.com:moderntribe/square1-post-meta.git",
		'src/Post_Type'     => "git@github.com:moderntribe/square1-post-type.git",
		'src/Queues'        => "git@github.com:moderntribe/square1-queues.git",
		'src/Queues_Mysql'  => "git@github.com:moderntribe/square1-queues-mysql.git",
		'src/Request'       => "git@github.com:moderntribe/square1-request.git",
		'src/Required_Page' => "git@github.com:moderntribe/square1-required-page.git",
		'src/Routes'        => "git@github.com:moderntribe/square1-routes.git",
		'src/Schema'        => "git@github.com:moderntribe/square1-schema.git",
		'src/Settings'      => "git@github.com:moderntribe/square1-settings.git",
		'src/Taxonomy'      => "git@github.com:moderntribe/square1-taxonomy.git",
		'src/Twig'          => "git@github.com:moderntribe/square1-twig.git",
		'src/User'          => "git@github.com:moderntribe/square1-user.git",
		'src/Utils'         => "git@github.com:moderntribe/square1-utils.git",
		'src/WP_Downloader' => "git@github.com:moderntribe/square1-wp-downloader.git",
		'src/Whoops'        => "git@github.com:moderntribe/square1-whoops.git",
	] );

	$services = $containerConfigurator->services();
	$services->defaults()->public()->autowire()->autoconfigure();

	// release workers - in order to execute
	$services->set( Symplify\MonorepoBuilder\Release\ReleaseWorker\SetCurrentMutualDependenciesReleaseWorker::class );
	$services->set( Symplify\MonorepoBuilder\Release\ReleaseWorker\AddTagToChangelogReleaseWorker::class );
	$services->set( Symplify\MonorepoBuilder\Release\ReleaseWorker\TagVersionReleaseWorker::class );
	$services->set( Symplify\MonorepoBuilder\Release\ReleaseWorker\PushTagReleaseWorker::class );
	$services->set( Symplify\MonorepoBuilder\Release\ReleaseWorker\SetNextMutualDependenciesReleaseWorker::class );

	$services->set( Tribe\Libs\Dev\Monorepo\ReleaseWorker\AddUnreleasedChangelogSection::class );
	$services->set( Tribe\Libs\Dev\Monorepo\ReleaseWorker\UpdateRootBranchAlias::class );

	$services->set( Symplify\MonorepoBuilder\Release\ReleaseWorker\UpdateBranchAliasReleaseWorker::class );
	$services->set( Symplify\MonorepoBuilder\Release\ReleaseWorker\PushNextDevReleaseWorker::class );
};
