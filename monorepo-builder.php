<?php declare(strict_types=1);

use Symplify\MonorepoBuilder\Config\MBConfig;
use Symplify\MonorepoBuilder\Release\ReleaseWorker;
use Tribe\Libs\Dev\Monorepo\ReleaseWorker\AddUnreleasedChangelogSection;
use Tribe\Libs\Dev\Monorepo\ReleaseWorker\UpdateRootBranchAlias;

return static function ( MBConfig $config ): void {
	$config->packageDirectories( [
		__DIR__ . '/src',
	] );

	// This should be updated if you're making a new major branch, e.g. `5.x`
	$config->defaultBranch( '4.x' );

	// Release workers, execute in the order they appear here.
	$config->workers( [
		ReleaseWorker\SetCurrentMutualDependenciesReleaseWorker::class,
		ReleaseWorker\AddTagToChangelogReleaseWorker::class,
		ReleaseWorker\TagVersionReleaseWorker::class,
		ReleaseWorker\PushTagReleaseWorker::class,
		ReleaseWorker\SetNextMutualDependenciesReleaseWorker::class,
		AddUnreleasedChangelogSection::class,
		UpdateRootBranchAlias::class,
		ReleaseWorker\UpdateBranchAliasReleaseWorker::class,
		ReleaseWorker\PushNextDevReleaseWorker::class,
	] );
};
