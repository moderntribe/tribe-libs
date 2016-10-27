<?php


namespace Tribe\Libs\Post_Type;


use Tribe\Libs\Post_Meta\Meta_Map;
use Tribe\Libs\Post_Meta\Meta_Repository;

/**
 * Class Post_Object
 *
 * Extend this class for each registered post type.
 * Be sure to set a value for the NAME constant in
 * each subclass.
 *
 * For most not-test usage, instances of this class
 * will be created via the `factory()` method called
 * on the subclasses.
 *
 * Instances can be used to access meta registered by
 * an appropriate Meta_Group, via the `get_meta()` method
 * called with a registered key.
 */
abstract class Post_Object {
	const NAME = '';

	/** @var Meta_Map */
	protected $meta;

	protected $post_id = 0;

	/**
	 * post
	 *
	 * @var \WP_Post
	 */
	protected $post;

	/**
	 * Post_Object constructor.
	 *
	 * @param int           $post_id        The ID of a WP post
	 * @param Meta_Map|null $meta           Meta fields appropriate to this post type.
	 *                                      If you're not sure what to do here, chances
	 *                                      are you should be calling self::get_post().
	 */
	public function __construct( $post_id = 0, Meta_Map $meta = NULL ) {
		$this->post_id = $post_id;
		if ( isset( $meta ) ) {
			$this->meta = $meta;
		} else {
			$this->meta = new Meta_Map( static::NAME );
		}
	}

	public function get_post_id(){
		return $this->post_id;
	}


	public function __get( $key ){
		$post = $this->get_post();
		if( isset( $post->{$key} ) ){
			return $this->{$key} = $post->{$key};
		}

		return $this->get_meta( $key );
	}


	public function get_post(){
		if( empty( $this->post ) ){
			$this->post = get_post( $this->post_id );
		}
		return $this->post;
	}


	/**
	 * Get the value for the given meta key corresponding
	 * to this post.
	 * 
	 * @param string $key
	 * @return mixed
	 */
	public function get_meta( $key ) {
		return $this->meta->get_value( $this->post_id, $key );
	}

	/**
	 * Get an instance of the Post_Object corresponding
	 * to the \WP_Post with the given $post_id
	 *
	 * @param int|\WP_Post $post
	 * @return static
	 */
	public static function factory( $post ) {
		/** @var Meta_Repository $meta_repo */
		$meta_repo = apply_filters( Meta_Repository::GET_REPO_FILTER, NULL );
		if ( !$meta_repo ) {
			$meta_repo = new Meta_Repository();
		}
		if( is_a( $post, 'WP_Post' ) ){
			$_post = new static( $post->ID, $meta_repo->get( static::NAME ) );
			$_post->post = $post;
		} else {
			$_post = new static( $post, $meta_repo->get( static::NAME ) );
		}
		return $_post;
	}
}