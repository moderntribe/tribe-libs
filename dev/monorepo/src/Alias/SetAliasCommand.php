<?php
declare( strict_types=1 );

namespace Tribe\Libs\Dev\Monorepo\Alias;

use PharIo\Version\Version;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\MonorepoBuilder\Configuration\Option;
use Symplify\MonorepoBuilder\DevMasterAliasUpdater;
use Symplify\MonorepoBuilder\FileSystem\ComposerJsonProvider;
use Symplify\PackageBuilder\Console\Command\CommandNaming;
use Symplify\PackageBuilder\Console\ShellCode;

/**
 * @TODO this needs to be updated for monorepo-builder 11.x
 */
class SetAliasCommand extends Command {

	/**
	 * @var SymfonyStyle
	 */
	private $symfonyStyle;
	/**
	 * @var DevMasterAliasUpdater
	 */
	private $devMasterAliasUpdater;
	/**
	 * @var ComposerJsonProvider
	 */
	private $composerJsonProvider;
	/**
	 * @var string
	 */
	private $packageAliasFormat;

	public function __construct(
		SymfonyStyle $symfonyStyle,
		DevMasterAliasUpdater $devMasterAliasUpdater,
		ComposerJsonProvider $composerJsonProvider,
		string $packageAliasFormat
	) {
		parent::__construct();

		$this->symfonyStyle          = $symfonyStyle;
		$this->devMasterAliasUpdater = $devMasterAliasUpdater;
		$this->composerJsonProvider  = $composerJsonProvider;
		$this->packageAliasFormat    = $packageAliasFormat;
	}

	protected function configure(): void {
		$this->setName( CommandNaming::classToName( self::class ) );
		$this->setDescription( 'Update the dev-master branch alias' );

		$description = 'Release version, in format "<major>.<minor>.<patch>" or "v<major>.<minor>.<patch>"';
		$this->addArgument( Option::VERSION, InputArgument::REQUIRED, $description );

		$this->addOption(
			Option::DRY_RUN,
			null,
			InputOption::VALUE_NONE,
			'Do not perform operations, just their preview'
		);
	}

	protected function execute( InputInterface $input, OutputInterface $output ): int {
		/** @var string $versionArgument */
		$versionArgument = $input->getArgument( Option::VERSION );
		$version         = new Version( $versionArgument );

		$isDryRun = (bool) $input->getOption( Option::DRY_RUN );

		if ( $isDryRun ) {
			$this->symfonyStyle->note( 'Running in dry mode, nothing is changed' );
		} else {
			$this->updateAlias( $version );
			$this->symfonyStyle->success( sprintf( 'Version "%s" is now released!', $version->getVersionString() ) );
		}

		return ShellCode::SUCCESS;
	}

	protected function updateAlias( Version $version ): void {
		$alias = $this->getAliasFormat( $version );
		$this->devMasterAliasUpdater->updateFileInfosWithAlias(
			$this->composerJsonProvider->getRootAndPackageFileInfos(),
			$alias
		);
	}


	protected function getAliasFormat( Version $version ): string {
		return str_replace(
			[ '<major>', '<minor>' ],
			[ $version->getMajor()->getValue(), $version->getMinor()->getValue() ],
			$this->packageAliasFormat
		);
	}
}
