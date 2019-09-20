<?php


namespace Tribe\Libs\Generators;

use Pimple\Container;
use Tribe\Libs\Container\Service_Provider;

class Generator_Provider extends Service_Provider {
	const FILE_SYSTEM = 'generator.file_system';
	const PATH        = 'generator.path';
	const CPT         = 'generator.cpt';
	const TAX         = 'generator.taxonomy';
	const CLI         = 'generator.cli';
	const SETTING     = 'generator.setting';
	const META        = 'generator.meta';

	public function register( Container $container ) {
		$this->file_system( $container );
		$this->cpt( $container );
		$this->tax( $container );
		$this->cli( $container );
		$this->setting( $container );
		$this->meta( $container );
		$this->init( $container );
	}

	protected function init( Container $container ) {
		add_action( 'init', function () use ( $container ) {
			if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
				return;
			}
			foreach ( $this->commands_to_register() as $key ) {
				$container[ $key ]->register();
			}
		}, 0, 0 );
	}

	protected function commands_to_register() {
		return [
			self::CPT,
			self::TAX,
			self::CLI,
			self::SETTING,
			self::META,
		];
	}

	protected function file_system( Container $container ) {
		$container[ self::FILE_SYSTEM ] = function ( $container ) {
			return new File_System();
		};

		$container[ self::PATH ] = trailingslashit( WP_PLUGIN_DIR ) . 'core/src/';
	}

	protected function cpt( Container $container ) {
		$container[ self::CPT ] = function ( $container ) {
			return new CPT_Generator( $container[ self::FILE_SYSTEM ], $container[ self::PATH ] );
		};
	}

	protected function tax( Container $container ) {
		$container[ self::TAX ] = function ( $container ) {
			return new Taxonomy_Generator( $container[ self::FILE_SYSTEM ], $container[ self::PATH ] );
		};
	}

	protected function cli( Container $container ) {
		$container[ self::CLI ] = function ( $container ) {
			return new CLI_Generator( $container[ self::FILE_SYSTEM ], $container[ self::PATH ] );
		};
	}

	protected function setting( Container $container ) {
		$container[ self::SETTING ] = function ( $container ) {
			return new Settings_Generator( $container[ self::FILE_SYSTEM ], $container[ self::PATH ] );
		};
	}

	protected function meta( Container $container ) {
		$container[ self::META ] = function ( $container ) {
			return new Meta_Importer( $container[ self::FILE_SYSTEM ], $container[ self::PATH ] );
		};
	}

}
