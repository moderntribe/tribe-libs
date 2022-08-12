<?php declare(strict_types=1);

namespace Tribe\Libs\Required_Page;

use LogicException;
use Tribe\Libs\ACF;
use WP_Post;

/**
 * Class Required_Page
 *
 * @abstract
 *
 * Automatically creates a page with the given config, and registers
 * a setting to reference the page.
 */
abstract class Required_Page {

	public const NAME = '';

	private string $field_group_key;

	/**
	 * @return string The title of the post
	 */
	abstract protected function get_title(): string;

	/**
	 * @return string The slug of the post
	 */
	abstract protected function get_slug(): string;

	public function __construct( string $field_group_key = '' ) {
		if ( ! static::NAME ) {
			throw new LogicException( __( 'NAME constant must be set', 'tribe' ) );
		}

		$this->field_group_key = $field_group_key;
	}

	/**
	 * Ensure that there is a page designated as this page
	 * at all times. Creates one if necessary.
	 *
	 * @action admin_init
	 */
	public function ensure_page_exists(): void {
		$post_id = $this->get_post_id();

		if ( ! empty( $post_id ) ) {
			return;
		}

		$post_id = $this->create_post();

		if ( ! empty( $post_id ) ) {
			$this->set_post_id( $post_id );
		}

		/**
		 * Triggered when a required page is created
		 *
		 * @param string $name    The identifier for the required page
		 * @param int    $post_id The ID of the created post
		 */
		do_action( 'tribe/project/required_page/created', static::NAME, $post_id );
	}

	/**
	 * @param int|string $post_id
	 *
	 * @action trashed_post
	 * @action deleted_post
	 */
	public function clear_option_on_delete( $post_id ): void {
		$existing = $this->get_post_id();

		if ( $existing === (int) $post_id ) {
			delete_field( static::NAME, 'option' );
		}

		/**
		 * Triggered when a page identified as required is deleted
		 *
		 * @param string $name The identifier for the required page
		 */
		do_action( 'tribe/project/required_page/deleted', static::NAME );
	}

	/**
	 * Adds a field to control the page selection to an existing meta group
	 *
	 * @action acf/init 20
	 */
	public function register_setting(): void {
		$config = $this->get_field_config();

		if ( ! $config ) {
			return;
		}

		acf_add_local_field( $config );
	}

	/**
	 * Add a post state to the list table indicating
	 * that this is a required page.
	 *
	 * @filter display_post_states
	 *
	 * @param array<string, string> $post_states
	 * @param \WP_Post              $post
	 *
	 * @return array<string, string>
	 */
	public function indicate_post_state( array $post_states, WP_Post $post ): array {

		if ( $this->get_post_id() === $post->ID ) {
			$label = $this->get_field_label();
			if ( $label ) {
				$post_states[ static::NAME ] = $label;
			}
		}

		return $post_states;
	}

	/**
	 * @return string The post type of the post that will be created
	 */
	protected function get_post_type(): string {
		return 'page';
	}

	/**
	 * @return string The content of the post
	 */
	protected function get_content(): string {
		return '';
	}

	/**
	 * Create the post for this config
	 *
	 * @return int the ID of the created post
	 */
	protected function create_post(): int {
		$args = $this->get_post_args();

		return wp_insert_post( $args );
	}

	/**
	 * @return mixed[] The args for creating the post
	 */
	protected function get_post_args(): array {
		$args = [
			'post_type'      => $this->get_post_type(),
			'post_status'    => 'publish',
			'post_title'     => $this->get_title(),
			'post_name'      => $this->get_slug(),
			'post_content'   => $this->get_content(),
			'comment_status' => 'closed',
			'ping_status'    => 'closed',
		];

		/**
		 * Filter the arguments for inserting a required page
		 *
		 * @param array  $args The arguments to pass to wp_insert_post
		 * @param string $name The identifier for the required page
		 */
		return apply_filters( 'tribe/project/required_page/post_args', $args, static::NAME );
	}

	/**
	 * @return mixed[] The field configuration array
	 */
	protected function get_field_config(): array {
		if ( empty( $this->field_group_key ) || ! class_exists( '\Tribe\Libs\ACF\Field' ) ) {
			return [];
		}

		$field = new ACF\Field( static::NAME );

		$field->set_attributes( [
			'parent'        => $this->field_group_key,
			'label'         => $this->get_field_label(),
			'name'          => static::NAME,
			'type'          => 'post_object',
			'post_type'     => $this->get_post_type(),
			'return_format' => 'int',
		] );

		return $field->get_attributes();
	}

	protected function get_field_label(): string {
		return sprintf( __( '%s Page', 'tribe' ), $this->get_title() );
	}

	/**
	 * @return int The ID of the post registered as this page
	 */
	private function get_post_id(): int {
		return (int) get_field( static::NAME, 'option', false );
	}

	/**
	 * Set the ID of the post registered as this page
	 *
	 * @param int|string $post_id
	 */
	private function set_post_id( $post_id ): void {
		update_field( static::NAME, (int) $post_id, 'option' );
	}

}
