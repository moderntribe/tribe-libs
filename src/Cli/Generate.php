<?php


namespace Tribe\Libs\Cli;

use Tribe\Project\Core;

class Generate extends \WP_CLI_Command {

	/**
	 * Generates a Post Type
	 *
	 * ## OPTIONS
	 *
	 * <name>
	 * : The name of the Post Type as you'd name the PHP class
	 *
	 * ## OPTIONS
	 *
	 * [--defaults]
	 * : Don't ask questions about the post type configuration. Use default assumptions.
	 *
	 * ## EXAMPLES
	 *
	 *     wp s1 generate post-type Person
	 *     wp s1 generate post-type Some_Object --shhh
	 *
	 * @subcommand post-type
	 * @alias      pt
	 */
	public function post_type( $args, $assoc_args ) {
		$post_type = $args[0];
		$slug      = str_replace( '_', '-', sanitize_title_with_dashes( $post_type ) );
		$singular  = trim( ucwords( str_replace( '_', ' ', $post_type ) ), 's' );

		$values = [
			'CLASS_NAME'     => [
				'question' => 'Class Name',
				'value'    => $post_type,
			],
			'POST_TYPE_NAME' => [
				'question' => 'Post Type name (as in the post_type db field)',
				'value'    => $slug,
			],
			'SINGULAR_NAME'  => [
				'question' => 'Singular name',
				'value'    => $singular,
			],
			'PLURAL_NAME'    => [
				'question' => 'Plural name',
				'value'    => $singular . 's',
			],
			'DASHICON'       => [
				'question' => 'Dashicon',
				'value'    => 'dashicons-no',
			],
		];

		if ( empty( $assoc_args['defaults'] ) ) {
			$values = $this->ask_questions( $values );
		}

		$this->build( $values['CLASS_NAME']['value'], 'Post_Type', $values );
		$this->build( $values['CLASS_NAME']['value'], 'Post_Type_Configuration', $values );

		$this->update_array( 'Global_Service_Provider', '/*DONTREMOVE*/', $values['CLASS_NAME']['value'] );


		\WP_CLI::success( "Post Type '{$values['CLASS_NAME']['value']}' generated." );
	}

	// ToDo: move to own class
	protected function ask_questions( $values ) {
		foreach ( $values as $key => $data ) {
			$question                = sprintf( '%s [%s]', $data['question'], $data['value'] );
			$values[ $key ]['value'] = \cli\prompt( $question, $data['value'] );
		}

		return $values;
	}

	// ToDo: move to own class
	public function build( $name, $object_type, $values ) {
		$settings = $this->settings();
		$template = $this->compile( $object_type, $values );
		$this->save( $name, $settings[ $object_type ], $template );
	}

	public function update_array( $file, $token, $content ) {
		$settings     = $this->settings();
		$path         = $settings[ $file ];
		$file_content = file_get_contents( $path );
		$content      = sprintf( "'%s',\n\t\t%s", $content, $token );
		$file_content = str_replace( $token, $content, $file_content );

		//file_put_contents( $path, $file_content );
		echo "<pre>";
		print_r( $file_content );
		echo "</pre>";


	}

	// ToDo: move to own class
	public function compile( $template_file, $values ) {
		$template = file_get_contents( trailingslashit( __DIR__ ) . 'Templates/' . $template_file . '.s1t' );

		foreach ( $values as $key => $data ) {
			$template = preg_replace( "/\\$$key\\$/i", $data['value'], $template );
		}

		return $template;
	}

	// ToDo: move to own class
	public function save( $file, $path, $code ) {
		file_put_contents( $path . $file . '.php', $code );
	}

	// ToDo: Move to own class and get them from a config file
	private function settings() {
		$core      = Core::instance();
		$container = $core->container();
		$core_path = plugin_dir_path( $container['plugin_file'] );

		return [
			'Post_Type'               => $core_path . 'src/Post_Types/',
			'Post_Type_Configuration' => $core_path . 'src/Post_Types/Config/',
			'Global_Service_Provider' => $core_path . 'src/Service_Providers/Global_Service_Provider.php',
		];
	}

}