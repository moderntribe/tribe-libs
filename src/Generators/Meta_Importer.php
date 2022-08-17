<?php

namespace Tribe\Libs\Generators;

use WP_CLI;

class Meta_Importer extends Generator_Command {

	protected $args       = [];
	protected $assoc_args = [];
	protected $group      = [];
	protected $key        = '';
	protected $title      = '';
	protected $slug       = '';
	protected $class_name = '';
	protected $namespace  = '';
	protected $const_name = '';
	protected $pimple_key = '';
	protected $constants  = [];

	public function description() {
		return __( 'Imports object meta created in ACF.', 'tribe' );
	}

	public function command() {
		return 'import meta';
	}

	public function arguments() {
		return [
			[
				'type'     => 'positional',
				'name'     => 'field_group',
				'optional' => true,
			],
			[
				'type'        => 'flag',
				'name'        => 'delete-group',
				'optional'    => true,
				'description' => __( 'Whether or not to delete the imported field group. Defaults to true, pass --no-delete-group if you wish to preserve the group.', 'tribe' ),
				'default'     => true,
			],
		];
	}

	public function run_command( $args, $assoc_args ) {
		$this->args       = $args;
		$this->assoc_args = $assoc_args;

		if ( empty( $this->get_dynamic_field_groups() ) ) {
			WP_CLI::error( __( 'There are zero field groups available to import', 'tribe' ) );
		}

		if ( ! count( $args ) ) {
			foreach ( $this->get_dynamic_field_groups() as $field_group_id => $field_group_name ) {
				WP_CLI::line( sprintf( __( 'You can import %s with `wp s1 import meta %s`', 'tribe' ), $field_group_name, $field_group_id ) );
			}
			WP_CLI::halt( 0 );
		}

		// Setup and import the field groups.
		$this->setup_field_group();

		// Sanity check.
		if ( $this->assoc_args['delete-group'] ) {
			WP_CLI::confirm( sprintf( __( 'Are you sure you want to delete the database entry %s field group and convert it to php?', 'tribe' ), $this->title ), $assoc_args );
		}

		// Write the meta files.
		$this->update_definer();
		$this->create_object_class();

		// Delete the field group.
		if ( $this->assoc_args['delete-group'] ) {
			$this->delete_field_group();
		}

		// Success!
		WP_CLI::line( __( 'We did it!', 'tribe' ) );
	}

	protected function get_dynamic_field_groups() {
		$field_groups = [];
		foreach ( acf_get_field_groups() as $field_group ) {

			// If it's already a php field group we won't do anything with it.
			if ( ! isset( $field_group['local'] ) || 'php' !== $field_group['local'] ) {
				$field_groups[ $field_group['key'] ] = $field_group['title'];
			}
		}

		return $field_groups;
	}

	protected function setup_field_group() {
		$group            = acf_get_field_group( $this->args[0] );
		$group['fields']  = acf_get_fields( $group );
		$this->group      = acf_prepare_field_group_for_export( $group );
		$this->key        = $this->group['key'];
		$this->title      = $this->group['title'];
		$this->slug       = $this->sanitize_slug( [ $this->title ] );
		$this->class_name = $this->ucwords( $this->slug );
		$this->namespace  = 'Tribe\Project\Object_Meta\\' . $this->class_name;
		$this->const_name = $this->file_system->constant_from_class( $this->slug );
		$this->pimple_key = strtolower( 'object_meta.' . $this->const_name );
	}

	protected function update_definer() {
		$definer = $this->src_path . 'Object_Meta/Object_Meta_Definer.php';

		// Keys.
		$key = "\t\t\t\tDI\get( {$this->class_name}::class )," . PHP_EOL;
		$this->file_system->insert_into_existing_file( $definer, $key, 'self::GROUPS => [' );

		// public function register( Container $container ) {
		$container_partial_file = file_get_contents( $this->templates_path . 'object_meta/container_partial.php' );
		$container_partial      = sprintf( $container_partial_file, $this->class_name, $this->file_system->format_array_for_file( $this->build_object_array(), 16 ), $this->const_name );
		$this->file_system->insert_into_existing_file( $definer, $container_partial, '->constructor( DI\get( self::GROUPS ) )' );
	}

	protected function build_object_array() {
		$locations = [];

		$accepted_locations = [
			'post_type'    => 'post_types',
			'taxonomy'     => 'taxonomies',
			'options_page' => 'settings_pages',
			'user_form'    => 'users',
		];

		foreach ( $this->group['location'] as $location ) {
			if ( array_key_exists( $location[0]['param'], $accepted_locations ) ) {
				$locations[ $accepted_locations[ $location[0]['param'] ] ][] = $location[0]['value'];
			}
		}

		if ( array_key_exists( 'users', $locations ) ) {
			$locations['users'] = true;
		}

		return $locations;
	}

	protected function create_object_class() {
		$object_class = trailingslashit( dirname( __DIR__, 2 ) ) . 'Object_Meta/' . $this->class_name . '.php';
		$this->file_system->write_file( $object_class, $this->class_file_template() );
	}

	protected function class_file_template() {
		$class_file = file_get_contents( $this->templates_path . 'object_meta/object_meta.php' );

		return sprintf(
			$class_file,
			$this->class_name,
			$this->slug,
			$this->field_keys(),
			$this->title,
			$this->add_field_functions( $this->group['fields'] ),
			$this->field_functions( $this->group['fields'] ),
			$this->field_constants()
		);
	}

	protected function field_constants() {
		$constants = '';

		foreach ( $this->constants as $label => $name ) {
			$constants .= "\tconst " . $this->file_system->constant_from_class( $this->sanitize_slug( [ $label ] ) ) . ' = ' . '\'' . $this->slug . '_' . $name . '\';' . PHP_EOL;
		}

		return $constants;
	}

	protected function field_keys() {
		$keys = '';
		foreach ( $this->group['fields'] as $field ) {
			$keys .= "\t\t\tstatic::" . $this->file_system->constant_from_class( $this->sanitize_slug( [ $field['label'] ] ) ) . ',' . PHP_EOL;
		}

		return $keys;
	}

	protected function add_field_functions( $fields, $group = '$group', $indent = 8 ) {
		$functions = '';
		foreach ( $fields as $field ) {
			$functions .= str_repeat( ' ', $indent ) . $group . '->add_field( $this->get_field_' . $this->sanitize_slug( [ $field['label'] ] ) . '() );' . PHP_EOL;
		}

		return $functions;
	}

	protected function field_functions( $fields ) {
		$function_partial = file_get_contents( $this->templates_path . 'object_meta/field_function_partial.php' );

		$functions = '';
		foreach ( $fields as $field ) {

			$this->constants[ $field['label'] ] = $field['name'];

			$field = $this->prepare_field( $field );

			$fields_containing_subfields = [
				'repeater',
				'group',
				'flexible_content',
			];

			if ( ! in_array( $field['type'], $fields_containing_subfields ) ) {
				$functions .= sprintf(
					$function_partial,
					$this->sanitize_slug( [ $field['label'] ] ),
					$this->file_system->constant_from_class( $this->sanitize_slug( [ $field['label'] ] ) ),
					$this->file_system->format_array_for_file( $field, 16 )
				);
			} else {
				$functions .= $this->get_repeater( $field );
			}
		}

		return $functions;
	}

	protected function delete_field_group() {
		acf_delete_field_group( acf_get_field_group_id( $this->group ) );
	}

	private function prepare_field( $field ) {
		unset( $field['key'], $field['wrapper'], $field['prepend'], $field['append'] );

		$field = array_filter( $field, fn( $element ) => '' !== $element );

		$zero_fields = [
			'required',
			'conditional_logic',
		];

		foreach ( $zero_fields as $zero_field ) {
			if ( 0 === $field[ $zero_field ] ) {
				unset( $field[ $zero_field ] );
			}
		}

		$required_fields = [
			'name',
			'label',
		];

		foreach ( $required_fields as $required_field ) {
			if ( ! isset( $field[ $required_field ] ) ) {
				WP_CLI::error( sprintf( __( '%s field must be set.', 'tribe' ), $required_field ) );
			}
		}

		return $field;
	}

	private function get_repeater( $field ) {
		$write_field = $field;
		unset( $write_field['sub_fields'] );

		$group_partial = file_get_contents( $this->templates_path . 'object_meta/repeater_function_partial.php' );
		$group         = sprintf(
			$group_partial,
			$this->sanitize_slug( [ $field['label'] ] ),
			$this->file_system->constant_from_class( $this->sanitize_slug( [ $field['label'] ] ) ),
			$this->file_system->format_array_for_file( $write_field, 16 ),
			$this->add_field_functions( $field['sub_fields'], '$repeater' )
		);

		return $group . $this->field_functions( $field['sub_fields'] );
	}
}
