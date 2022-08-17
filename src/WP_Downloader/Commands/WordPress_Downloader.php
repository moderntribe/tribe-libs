<?php declare(strict_types=1);

namespace Tribe\Libs\WP_Downloader\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Tribe\Libs\WP_Downloader\Installer;
use Tribe\Libs\WP_Downloader\Wordpress;

class WordPress_Downloader extends Command {

	public const ARG_VERSION = 'version';
	public const OPTION_PATH = 'path';

	protected static $defaultName = 'wp';
	protected static $defaultDescription = 'Downloads WordPress to a specified path';

	protected Wordpress $wordpress;
	protected Installer $installer;

	public function __construct( Wordpress $wordpress, Installer $installer, string $name = null ) {
		$this->wordpress = $wordpress;
		$this->installer = $installer;

		parent::__construct( $name );
	}

	protected function configure() {
		$this->addArgument( self::ARG_VERSION, InputArgument::OPTIONAL, 'The WordPress version to download', 'latest' );
		$this->addOption( self::OPTION_PATH, 'p', InputOption::VALUE_REQUIRED, 'The directory relative to the project root where you want to install WordPress' );
	}

	protected function execute( InputInterface $input, OutputInterface $output ): int {
		$requested_version = $input->getArgument( self::ARG_VERSION );
		$release           = $this->wordpress->find_version( $requested_version );
		$path              = $input->getOption( self::OPTION_PATH );

		if ( ! $path ) {
			$output->writeln( 'Error: --path cannot be empty.' );

			return Command::FAILURE;
		}

		if ( ! $release ) {
			$output->writeln( sprintf( 'Error: Could not find WordPress version: %s.', $requested_version ) );

			return Command::FAILURE;
		}

		if ( $this->installer->install( $release->download, sprintf( 'wordpress-%s', $release->version ), $path, $output ) ) {
			return Command::SUCCESS;
		}

		return Command::FAILURE;
	}

}
