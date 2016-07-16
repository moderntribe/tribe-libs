<?php


namespace Tribe\Libs\Cli;

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
				'question' => 'Singular name',
				'value'    => $singular . 's',
			],
		];

		if ( empty( $assoc_args['defaults'] ) ) {
			$values = $this->ask_questions( $values );
		}
		
		
		echo "<pre>";
		print_r($values);
		echo "</pre>";
		
		


	}

	// ToDo: move to own class
	protected function ask_questions( $values ) {
		foreach ( $values as $key => $data ) {
			$question = sprintf( '%s [%s]', $data['question'], $data['value'] );
			$values[ $key ]['value'] = \cli\prompt( $question, $data['value'] );
		}

		return $values;
	}

	// ToDo: move to own class
	public function compile( $template_file, $values ) {
	}

	// ToDo: move to 'compile' class
	public function compile_text( $template, $data ) {
		foreach ( $data as $key => $value ) {
			$template = preg_replace( "/\\$$key\\$/i", $value, $template );
		}

		return $template;
	}

	// ToDo: Move to own class and get them from a config file
	private function settings() {
		return [
			'post_type_path'               => '',
			'post_type_configuration_path' => '',
		];
	}

}