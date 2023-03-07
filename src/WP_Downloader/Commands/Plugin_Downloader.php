<?php declare(strict_types=1);

namespace Tribe\Libs\WP_Downloader\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Tribe\Libs\WP_Downloader\Installer;
use Tribe\Libs\WP_Downloader\Plugin;

class Plugin_Downloader extends Command {

	public const ARG_SLUG    = 'slug';
	public const ARG_VERSION = 'version';
	public const OPTION_PATH = 'path';

	protected static $defaultName = 'plugin';
	protected static $defaultDescription = 'Downloads a WordPress plugin to a specified path in the project';

	protected Plugin $plugin;
	protected Installer $installer;

	public function __construct( Plugin $plugin, Installer $installer, string $name = null ) {
		$this->plugin    = $plugin;
		$this->installer = $installer;

		parent::__construct( $name );
	}

	protected function configure() {
		$this->addArgument( self::ARG_SLUG, InputArgument::REQUIRED, 'The plugin slug to install' );
		$this->addArgument( self::ARG_VERSION, InputArgument::OPTIONAL, 'The plugin version', 'latest' );
		$this->addOption( self::OPTION_PATH, 'p', InputOption::VALUE_REQUIRED, 'The path to the plugins folder where the plugin will be installed' );
	}

	protected function execute( InputInterface $input, OutputInterface $output ): int {
		$slug              = $input->getArgument( self::ARG_SLUG );
		$requested_version = $input->getArgument( self::ARG_VERSION );
		$path              = $input->getOption( self::OPTION_PATH );
		$plugin_url        = $this->plugin->find( $slug, $requested_version );

		if ( ! $path ) {
			$output->writeln( 'Error: --path cannot be empty.' );

			return Command::FAILURE;
		}

		if ( ! $plugin_url ) {
			$output->writeln( sprintf( 'Error: Could not find version "%s" for plugin slug "%s".', $requested_version, $slug ) );

			return Command::FAILURE;
		}

		if ( $this->installer->install( $plugin_url, sprintf( '%s-%s', $slug, $requested_version ), $path, $output ) ) {
			return Command::SUCCESS;
		}

		return Command::FAILURE;
	}

}
