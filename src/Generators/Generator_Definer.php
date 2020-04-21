<?php
declare( strict_types=1 );

namespace Tribe\Libs\Generators;

use DI;
use Psr\Container\ContainerInterface;
use Tribe\Libs\CLI\CLI_Definer;
use Tribe\Libs\Container\Definer_Interface;

class Generator_Definer implements Definer_Interface {
	public const PATH = 'generator.path';

	public function define(): array {
		return [
			self::PATH => function ( ContainerInterface $container ) {
				return plugin_dir_path( $container->get( 'plugin.file' ) ) . 'src';
			},

			File_System::class => DI\create(),

			CLI_Generator::class      => DI\create()->constructor( DI\get( File_System::class ), DI\get( self::PATH ) ),
			CPT_Generator::class      => DI\create()->constructor( DI\get( File_System::class ), DI\get( self::PATH ) ),
			Settings_Generator::class => DI\create()->constructor( DI\get( File_System::class ), DI\get( self::PATH ) ),
			Taxonomy_Generator::class => DI\create()->constructor( DI\get( File_System::class ), DI\get( self::PATH ) ),
			Meta_Importer::class      => DI\create()->constructor( DI\get( File_System::class ), DI\get( self::PATH ) ),

			/**
			 * Add commands for the CLI subscriber to register
			 */
			CLI_Definer::COMMANDS     => DI\add( [
				DI\get( CLI_Generator::class ),
				DI\get( CPT_Generator::class ),
				DI\get( Settings_Generator::class ),
				DI\get( Taxonomy_Generator::class ),
				DI\get( Meta_Importer::class ),
			] ),
		];
	}
}
