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

		$properties = array_filter( array_map( [
			$this,
			'prepare_property',
		], explode( ',', $assoc_args['properties'] ?? '' ) ) );

		if ( $make_template ) {
			$this->make_template( $name, $path, $properties );
		}

		if ( $make_context ) {
			$this->make_context( $name, $path, $properties );
		}

		if ( $make_controller ) {
			$this->make_controller( $name, $path, $properties );
		}

		\WP_CLI::success( 'Way to go! ' . \WP_CLI::colorize( "%W{$name}%n" ) . ' component has been created' );
	}

	private function prepare_property( string $name ): array {
		$name = sanitize_title( trim( $name ) );
		if ( $name === 'classes' || $name === 'attrs' ) {
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

	private function make_template( $name, $path, $properties ): void {
		$template_file = $this->component_directory( $path, $name ) . $name . '.twig';
		$props         = implode( "\n\t", array_map( static function ( $prop ) {
			return sprintf( '{{ %s }}', $prop['name'] );
		}, $properties ) );

		$template_contents = sprintf(
			file_get_contents( __DIR__ . '/templates/component/template.twig' ),
			$props
		);

		\WP_CLI::log( 'Writing template file to ' . $template_file );
		\WP_CLI::debug( 'Template contents: ' . "\n" . $template_contents );

		$this->file_system->write_file( $template_file, $template_contents );
	}

	private function make_context( $name, $path, $properties ): void {
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

		$context_file     = $this->component_directory( $path, $name ) . $classname . '.php';
		$context_contents = sprintf(
			file_get_contents( __DIR__ . '/templates/component/context.php' ),
			$name,
			$classname,
			$namespace,
			$property_docblock,
			$property_constants,
			$property_declarations
		);

		\WP_CLI::log( 'Writing context file to ' . $context_file );
		\WP_CLI::debug( 'Context contents: ' . "\n" . $context_contents );

		$this->file_system->write_file( $context_file, $context_contents );
	}

	private function make_controller( $name, $path, $properties ): void {
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

		$controller_file     = $this->controller_directory( $path, $name ) . $classname . '.php';
		$controller_contents = sprintf(
			file_get_contents( __DIR__ . '/templates/component/controller.php' ),
			$classname,
			$controller_namespace,
			$context_namespace,
			$context_alias,
			$property_defaults
		);

		\WP_CLI::log( 'Writing controller file to ' . $controller_file );
		\WP_CLI::debug( 'Controller contents: ' . "\n" . $controller_contents );

		$this->file_system->write_file( $controller_file, $controller_contents );
	}

	protected function class_name( string $component_name ): string {
		$parts = array_map( 'ucwords', explode( '-', $component_name ) );

		return implode( '_', $parts );
	}
}
