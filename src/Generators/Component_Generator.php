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
				'description' => __( 'Whether to generate a placeholder template', 'tribe' ),
				'default'     => true,
			],
			[
				'type'        => 'flag',
				'name'        => 'controller',
				'optional'    => true,
				'description' => __( 'Whether to generate a placeholder Controller class', 'tribe' ),
				'default'     => true,
			],
			[
				'type'        => 'flag',
				'name'        => 'css',
				'optional'    => true,
				'description' => __( 'Whether to generate a placeholder css file', 'tribe' ),
				'default'     => true,
			],
			[
				'type'        => 'flag',
				'name'        => 'js',
				'optional'    => true,
				'description' => __( 'Whether to generate a placeholder js file', 'tribe' ),
				'default'     => true,
			],
			[
				'type'        => 'flag',
				'name'        => 'dry-run',
				'optional'    => true,
				'description' => __( 'During a dry-run, no files will be written', 'tribe' ),
				'default'     => false,
			],
		];
	}

	public function run_command( $args, $assoc_args ) {

		// a name of "path/to/something" should give us $path = [ 'path', 'to' ], $name = 'something'
		$path = array_map( 'sanitize_title', explode( '/', $args[0] ) );
		$name = array_pop( $path );

		$make_template   = get_flag_value( $assoc_args, 'template', true );
		$make_controller = get_flag_value( $assoc_args, 'controller', true );
		$make_css        = get_flag_value( $assoc_args, 'css', true );
		$make_js         = get_flag_value( $assoc_args, 'js', true );
		$dry_run         = get_flag_value( $assoc_args, 'dry-run', false );

		if ( $make_template ) {
			$this->make_template( $name, $path, $dry_run );
		}

		if ( $make_controller ) {
			$this->make_controller( $name, $path, $dry_run );
		}

		if ( $make_css ) {
			$this->make_css( $name, $path, $dry_run );
		}

		if ( $make_js ) {
			$this->make_js( $name, $path, $dry_run );
		}

		\WP_CLI::success( 'Way to go! ' . \WP_CLI::colorize( "%W{$name}%n" ) . ' component has been created' );
	}

	private function component_directory( array $path, $name ): string {
		return trailingslashit( $this->theme_path . 'components/' . implode( '/', $path ) ) . $name . '/';
	}

	private function controller_namespace( array $path, string $name ): string {
		$namespace = 'Tribe\\Project\\Templates\\Components\\';
		if ( ! empty( $path ) ) {
			$namespace .= implode( '\\', $path ) . '\\';
		}
		$namespace .= $name;

		return $namespace;
	}

	private function controller_classname( string $name ): string {
		return $this->class_name( $name ) . '_Controller';
	}

	private function make_template( $name, $path, $dry_run ): void {
		$directory        = $this->component_directory( $path, $name );
		$template_file    = $directory . $name . '.php';
		$namespace        = $this->controller_namespace( $path, $name );
		$class_name       = $this->controller_classname( $name );

		$template_contents = sprintf(
			file_get_contents( __DIR__ . '/templates/component/template.php.tmpl' ),
			$namespace,
			$class_name
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

	private function make_controller( $name, $path, $dry_run ): void {
		$classname = $this->controller_classname( $name );
		$namespace = $this->controller_namespace( $path, $name );

		$directory           = $this->component_directory( $path, $name );
		$controller_file     = $directory . $classname . '.php';
		$controller_contents = sprintf(
			file_get_contents( __DIR__ . '/templates/component/controller.php.tmpl' ),
			$classname,
			$namespace
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
		$directory   = $this->component_directory( $path, $name );
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
		$directory   = $this->component_directory( $path, $name );
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
		$parts = array_map( 'ucwords', preg_split( '/(-|_)/', $component_name ) );

		return implode( '_', $parts );
	}

	protected function human_name( string $component_name ): string {
		$parts = array_map( 'ucwords', preg_split( '/(-|_)/', $component_name ) );

		return implode( ' ', $parts );
	}
}
