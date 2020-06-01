<?php

namespace Tribe\Libs\Generators;

use function WP_CLI\Utils\get_flag_value;

class Component_Generator extends Generator_Command {
	/**
	 * @var string Path to the theme's directory, with a trailing slash
	 */
	private $theme_path;

	public function __construct( File_System $file_system, $src_path, $theme_path ) {
		parent::__construct( $file_system, $src_path );
		$this->theme_path = trailingslashit( $theme_path );
	}


	public function description() {
		return __( 'Generates a component.', 'tribe' );
	}

	public function command() {
		return 'generate component';
	}

	public function arguments() {
		return [
			[
				'type'        => 'positional',
				'name'        => 'component',
				'optional'    => false,
				'description' => __( 'The name of the component', 'tribe' ),
			],
			[
				'type'        => 'flag',
				'name'        => 'template',
				'optional'    => true,
				'description' => __( 'Whether to generate a placeholder Twig template', 'tribe' ),
				'default'     => true,
			],
			[
				'type'        => 'flag',
				'name'        => 'context',
				'optional'    => true,
				'description' => __( 'Whether to generate a placeholder Context class', 'tribe' ),
				'default'     => true,
			],
			[
				'type'        => 'flag',
				'name'        => 'controller',
				'optional'    => true,
				'description' => __( 'Whether to generate a placeholder Controller class', 'tribe' ),
				'default'     => false,
			],
			[
				'type'        => 'flag',
				'name'        => 'css',
				'optional'    => true,
				'description' => __( 'Whether to generate a placeholder css file', 'tribe' ),
				'default'     => false,
			],
			[
				'type'        => 'flag',
				'name'        => 'js',
				'optional'    => true,
				'description' => __( 'Whether to generate a placeholder js file', 'tribe' ),
				'default'     => false,
			],
			[
				'type'        => 'flag',
				'name'        => 'dry-run',
				'optional'    => true,
				'description' => __( 'During a dry-run, no files will be written', 'tribe' ),
				'default'     => false,
			],
			[
				'type'        => 'assoc',
				'name'        => 'properties',
				'optional'    => true,
				'description' => __( 'A comma separated list of properties to define for the controller. "classes" and "attrs" will always be created', 'tribe' ),
			],
		];
	}

	public function run_command( $args, $assoc_args ) {

		// a name of "path/to/something" should give us $path = [ 'path', 'to' ], $name = 'something'
		$path = array_map( 'sanitize_title', explode( '/', $args[0] ) );
		$name = array_pop( $path );

		$make_template   = get_flag_value( $assoc_args, 'template', true );
		$make_context    = get_flag_value( $assoc_args, 'context', true );
		$make_controller = get_flag_value( $assoc_args, 'controller', false );
		$make_css        = get_flag_value( $assoc_args, 'css', false );
		$make_js         = get_flag_value( $assoc_args, 'js', false );
		$dry_run         = get_flag_value( $assoc_args, 'dry-run', false );

		$properties = array_filter( array_map( [
			$this,
			'prepare_property',
		], explode( ',', $assoc_args['properties'] ?? '' ) ) );

		if ( $make_template ) {
			$this->make_template( $name, $path, $properties, $dry_run );
		}

		if ( $make_context ) {
			$this->make_context( $name, $path, $properties, $dry_run );
		}

		if ( $make_controller ) {
			$this->make_controller( $name, $path, $properties, $dry_run );
		}

		if ( $make_css ) {
			$this->make_css( $name, $path, $dry_run );
		}

		if ( $make_js ) {
			$this->make_js( $name, $path, $dry_run );
		}

		\WP_CLI::success( 'Way to go! ' . \WP_CLI::colorize( "%W{$name}%n" ) . ' component has been created' );
	}

	private function prepare_property( string $name ): array {
		$name = sanitize_title( trim( $name ) );
		if ( $name === 'classes' || $name === 'attrs' || empty( $name ) ) {
			return [];
		}
		$name  = str_replace( '-', '_', $name );
		$const = strtoupper( $name );

		return [
			'name'  => $name,
			'const' => $const,
		];
	}

	private function component_directory( array $path, $name ): string {
		return trailingslashit( $this->theme_path . 'components/' . implode( '/', $path ) ) . $name . '/';
	}

	private function controller_directory( array $path, $name ): string {
		$path = array_map( [ $this, 'class_name' ], $path );

		return trailingslashit( $this->src_path . 'Templates/Controllers/' . implode( '/', $path ) );
	}

	private function make_template( $name, $path, $properties, $dry_run ): void {
		$directory = $this->component_directory( $path, $name );
		$template_file = $directory . $name . '.twig';
		$props         = implode( "\n\t", array_map( static function ( $prop ) {
			return sprintf( '{{ %s }}', $prop['name'] );
		}, $properties ) );

		$template_contents = sprintf(
			file_get_contents( __DIR__ . '/templates/component/template.twig' ),
			$props
		);


		if ( $dry_run ) {
			\WP_CLI::log( '[Dry Run] Template file: ' . $template_file );
			\WP_CLI::log( 'Template contents: ' . "\n" . $template_contents );
		} else {
			\WP_CLI::log( 'Writing template file to ' . $template_file );
			$this->file_system->create_directory( $directory );
			$this->file_system->write_file( $template_file, $template_contents );
		}
	}

	private function make_context( $name, $path, $properties, $dry_run ): void {
		$directory = $this->component_directory( $path, $name );
		$classname = $this->class_name( $name );
		$namespace = 'Tribe\Project\Templates\Components';
		foreach ( $path as $path_part ) {
			$namespace .= '\\' . $this->class_name( $path_part );
		}

		$property_docblock     = implode( "\n * ", array_map( function ( $prop ) {
			return sprintf( '@property string   $%s', $prop['name'] );
		}, $properties ) );
		$property_constants    = implode( "\n\t", array_map( function ( $prop ) {
			return sprintf( "public const %s = '%s';", $prop['const'], $prop['name'] );
		}, $properties ) );
		$property_declarations = implode( "\n\t\t", array_map( function ( $prop ) {
			return sprintf( "self::%s => [\n\t\t\tself::DEFAULT => '',\n\t\t],", $prop['const'] );
		}, $properties ) );

		$context_file     = $directory . $classname . '.php';
		$context_contents = sprintf(
			file_get_contents( __DIR__ . '/templates/component/context.php' ),
			$name,
			$classname,
			$namespace,
			$property_docblock,
			$property_constants,
			$property_declarations
		);

		\WP_CLI::debug( 'Context contents: ' . "\n" . $context_contents );

		if ( $dry_run ) {
			\WP_CLI::log( '[Dry Run] Context file: ' . $context_file );
			\WP_CLI::log( 'Context contents: ' . "\n" . $context_contents );
		} else {
			\WP_CLI::log( 'Writing context file to ' . $context_file );
			$this->file_system->create_directory( $directory );
			$this->file_system->write_file( $context_file, $context_contents );
		}
	}

	private function make_controller( $name, $path, $properties, $dry_run ): void {
		$classname            = $this->class_name( $name );
		$controller_namespace = 'Tribe\Project\Templates\Controllers';
		$context_namespace    = 'Tribe\Project\Templates\Components';
		foreach ( $path as $path_part ) {
			$context_namespace    .= '\\' . $this->class_name( $path_part );
			$controller_namespace .= '\\' . $this->class_name( $path_part );
		}
		$context_alias = $classname . '_Context';

		$property_defaults = implode( "\n\t\t\t", array_map( function ( $prop ) use ( $context_alias ) {
			return sprintf( "%s::%s => '',", $context_alias, $prop['const'] );
		}, $properties ) );

		$directory = $this->controller_directory( $path, $name );
		$controller_file     = $directory . $classname . '.php';
		$controller_contents = sprintf(
			file_get_contents( __DIR__ . '/templates/component/controller.php' ),
			$classname,
			$controller_namespace,
			$context_namespace,
			$context_alias,
			$property_defaults
		);

		if ( $dry_run ) {
			\WP_CLI::log( '[Dry Run] Controller file: ' . $controller_file );
			\WP_CLI::log( 'Controller contents: ' . "\n" . $controller_contents );
		} else {
			\WP_CLI::log( 'Writing controller file to ' . $controller_file );
			$this->file_system->create_directory( $directory );
			$this->file_system->write_file( $controller_file, $controller_contents );
		}
	}

	private function make_css( $name, $path, $dry_run ): void {
		$directory = $this->component_directory( $path, $name );
		$index_file  = $directory . 'index.pcss';
		$source_file = $directory . 'css/' . $name . '.pcss';
		$human_name  = $this->human_name( $name );

		$index_contents  = sprintf(
			file_get_contents( __DIR__ . '/templates/component/index.pcss' ),
			$human_name,
			$name
		);
		$source_contents = sprintf(
			file_get_contents( __DIR__ . '/templates/component/source.pcss' ),
			$human_name
		);
		if ( $dry_run ) {
			\WP_CLI::log( '[Dry Run] CSS index file: ' . $index_file );
			\WP_CLI::log( 'CSS index contents: ' . "\n" . $index_contents );
			\WP_CLI::log( '[Dry Run] CSS source file: ' . $source_file );
			\WP_CLI::log( 'CSS source contents: ' . "\n" . $source_contents );
		} else {
			\WP_CLI::log( 'Writing CSS index file to ' . $index_file );
			$this->file_system->create_directory( $directory );
			$this->file_system->write_file( $index_file, $index_contents );
			\WP_CLI::log( 'Writing CSS source file to ' . $source_file );
			$this->file_system->create_directory( $directory . 'css/' );
			$this->file_system->write_file( $source_file, $source_contents );
		}
	}

	private function make_js( $name, $path, $dry_run ): void {
		$directory = $this->component_directory( $path, $name );
		$index_file  = $directory . 'index.js';
		$source_file = $directory . 'js/' . $name . '.js';
		$human_name  = $this->human_name( $name );

		$index_contents  = sprintf(
			file_get_contents( __DIR__ . '/templates/component/index.js' ),
			$human_name,
			$name
		);
		$source_contents = sprintf(
			file_get_contents( __DIR__ . '/templates/component/source.js' ),
			$human_name
		);

		if ( $dry_run ) {
			\WP_CLI::log( '[Dry Run] JS index file: ' . $index_file );
			\WP_CLI::log( 'JS index contents: ' . "\n" . $index_contents );
			\WP_CLI::log( '[Dry Run] JS source file: ' . $source_file );
			\WP_CLI::log( 'JS source contents: ' . "\n" . $source_contents );
		} else {
			\WP_CLI::log( 'Writing JS index file to ' . $index_file );
			$this->file_system->create_directory( $directory );
			$this->file_system->write_file( $index_file, $index_contents );
			\WP_CLI::log( 'Writing JS source file to ' . $source_file );
			$this->file_system->create_directory( $directory . 'js/' );
			$this->file_system->write_file( $source_file, $source_contents );
		}
	}

	protected function class_name( string $component_name ): string {
		$parts = array_map( 'ucwords', explode( '-', $component_name ) );

		return implode( '_', $parts );
	}

	protected function human_name( string $component_name ): string {
		$parts = array_map( 'ucwords', explode( '-', $component_name ) );

		return implode( ' ', $parts );
	}
}
