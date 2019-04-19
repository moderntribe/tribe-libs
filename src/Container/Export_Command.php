<?php

namespace Tribe\Libs\Container;

use Pimple\Container;
use Tribe\Libs\CLI\Command;

class Export_Command extends Command {
	/** @var object */
	private $project;

	/** @var Container */
	private $container;

	/**
	 * Export_Command constructor.
	 *
	 * @param object    $project
	 * @param Container $container
	 */
	public function __construct( $project, Container $container ) {
		$this->project = $project;
		$this->container = $container;
		parent::__construct();
	}

	protected function command() {
		return 'container export';
	}

	protected function description() {
		return 'Exports the files needed to autocomplete container names and methods in PhpStorm';
	}

	protected function arguments() {
		return [];
	}

	public function run_command( $args, $assoc_args ) {
		try {
			// assumes the passed project has a property "providers" that contains an array of Service_Provider objects
			$reflection = new \ReflectionObject( $this->project );
			$providers = $reflection->getProperty( 'providers' );
			$providers->setAccessible( true );
			$exporter = new Exporter( $providers->getValue( $this->project ) );
		} catch ( \ReflectionException $e ) {
			$exporter = new Exporter( [] );
		}
		$exporter->dumpPhpstorm( $this->container );
		\WP_CLI::success( 'Done!' );
	}

}