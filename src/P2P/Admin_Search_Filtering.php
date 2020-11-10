<?php

namespace Tribe\Libs\P2P;

use Tribe\Libs\Assets\Asset_Loader;

class Admin_Search_Filtering {

	/**
	 * @var array[] A p2p_type to options list
	 */
	protected static $post_type_options = [ ];

	/**
	 * @var string A p2p relationship ID
	 */
	private $relationship_id = '';

	/**
	 * @var string Which side of the relationship to filter. Valid options are 'both', 'from', or 'to'
	 */
	private $side = 'both';

	private $from_post_types = [];
	private $to_post_types   = [];

	/** @var \P2P_Directed_Connection_Type */
	private $p2p_box_connection = null;

	private $side_to_show = '';

	public function __construct( Relationship $relationship, $side = 'both' ) {
		$this->relationship_id = $relationship::NAME;
		$this->from_post_types = $relationship->from();
		$this->to_post_types   = $relationship->to();
		$this->side = $side;
	}

	/**
	 * @return void
	 *
	 * @action load-post.php
	 * @action load-post-new.php
	 */
	public function add_post_page_hooks() {
		add_action( 'admin_enqueue_scripts', [ $this, 'prepare_js_data' ], 10, 0 );
		add_action( 'admin_print_scripts', [ __CLASS__, 'print_scripts' ], 11, 0 );
		add_filter( 'p2p_admin_box_show', [ $this, 'track_shown_boxes' ], 1000, 3 );
	}

	/**
	 * @param bool $shown
	 * @param \P2P_Directed_Connection_Type $directed
	 * @param \WP_Post $post
	 * @return bool
	 */
	public function track_shown_boxes( $shown, $directed, $post ) {
		$name = $directed->name;
		$side = $directed->get_direction();
		if ( $shown && $name === $this->relationship_id ) {
			if ( $this->side === 'both' || $side === $this->side ) {
				$this->p2p_box_connection = $directed;
				$this->side_to_show = $side;
			}
		}
		return $shown; // passthrough
	}

	public function prepare_js_data() {
		$post_types = $this->get_post_type_options( $this->side_to_show );
		if ( empty( $post_types ) ) {
			return;
		}

		self::$post_type_options[ $this->relationship_id ] = $post_types;
	}

	public static function print_scripts() {
		if ( empty( self::$post_type_options ) ) {
			return;
		}

		$css = file_get_contents( __DIR__ . '/assets/p2p-posttype-filter.css' );

		echo '<style type="text/css">' . $css . '</style>';

		$js = file_get_contents( __DIR__ . '/assets/p2p-posttype-filter.js' );
		$data = [
			'relationships' => array_keys(self::$post_type_options),
			'post_types'    => self::$post_type_options,
		];

		echo "\n<script>\n";
		echo 'var Tribe_P2P_Posttype_Filter = ' . wp_json_encode( $data ) . ';';
		echo $js;
		echo "\n</script>\n";
	}

	protected function get_p2p_type() {
		return $this->relationship_id;
	}

	protected function get_post_type_options( $opposite_side ) {
		if ( $opposite_side === 'from' ) {
			$post_types = $this->to_post_types;
		} elseif ( $opposite_side === 'to' ) {
			$post_types = $this->from_post_types;
		} else {
			return [];
		}

		$options = [];
		if ( count( $post_types ) > 1 ) {
			$options[0] = __( 'Everything', 'tribe' );
		}
		foreach ( $post_types as $pt ) {
			$pto = get_post_type_object( $pt );
			if ( $pto ) {
				$options[ $pt ] = $pto->label;
			}
		}
		return $options;
	}

	/**
	 * @param array $query_vars
	 * @param \P2P_Directed_Connection_Type $connection
	 * @param \WP_Post $post
	 * @return mixed
	 *
	 * @filter p2p_connectable_args
	 */
	public function filter_connectable_query_args( $query_vars, $connection, $post ) {
		if ( $this->should_filter_query_args() ) {
			$query_vars['post_type'] = $_REQUEST['post_type'];
		}

		return $query_vars;
	}

	protected function should_filter_query_args() {
		if ( empty( $_REQUEST['p2p_type'] ) || !$this->relationship_id == $_REQUEST['p2p_type'] ) {
			return false;
		}
		if ( empty( $_REQUEST['action'] ) || $_REQUEST['action'] != 'p2p_box' ) {
			return false;
		}
		if ( empty( $_REQUEST['subaction'] ) || $_REQUEST['subaction'] != 'search' ) {
			return false;
		}
		if ( empty( $_REQUEST['post_type'] ) ) {
			return false;
		}

		return true;
	}

}
