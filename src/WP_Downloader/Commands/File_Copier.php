<?php declare(strict_types=1);

namespace Tribe\Libs\WP_Downloader\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class File_Copier extends Command {

	public const ARG_FROM = 'from';
	public const ARG_TO   = 'to';

	protected static $defaultName = 'copy';
	protected static $defaultDescription = 'Copies a file from one place to another. Useful for sample files.';

	protected string $project_root;

	public function __construct( string $project_root, string $name = null ) {
		$this->project_root = $project_root;

		parent::__construct( $name );
	}

	protected function configure() {
		$this->addArgument( self::ARG_FROM, InputArgument::REQUIRED, 'The from path to the file, relative to the project root' );
		$this->addArgument( self::ARG_TO, InputArgument::REQUIRED, 'The to path to the file, relative to the project root' );
	}

	protected function execute( InputInterface $input, OutputInterface $output ): int {
		$from = realpath( $this->project_root ) . '/' . $input->getArgument( self::ARG_FROM );
		$to   = realpath( $this->project_root ) . '/' . $input->getArgument( self::ARG_TO );

		if ( ! file_exists( $to ) ) {
			$output->writeln( sprintf( 'Copying file %s to %s...', $from, $to ) );

			if ( copy( $from, $to ) ) {
				return Command::SUCCESS;
			} else {
				return Command::FAILURE;
			}
		} else {
			$output->writeln( sprintf( 'File "%s" already exists. Skipping...', $to ) );
		}

		return Command::SUCCESS;
	}

}
