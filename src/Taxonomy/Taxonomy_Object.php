<?php


namespace Tribe\Libs\Taxonomy;


use Tribe\Libs\Object_Meta\Meta_Map;
use Tribe\Libs\Object_Meta\Meta_Repository;

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
abstract class Taxonomy_Object {
	const NAME = '';

	/** @var Meta_Map */
	protected $meta;

	protected $taxonomy_id = 0;

	/**
	 * Post_Object constructor.
	 *
	 * @param int           $taxonomy_id    The ID of a taxonomy
	 * @param Meta_Map|null $meta           Meta fields appropriate to this post type.
	 *                                      If you're not sure what to do here, chances
	 *                                      are you should be calling self::get_post().
	 */
	public function __construct( $taxonomy_id = 0, Meta_Map $meta = NULL ) {
		$this->taxonomy_id = $taxonomy_id;
		if ( isset( $meta ) ) {
			$this->meta = $meta;
		} else {
			$this->meta = new Meta_Map( static::NAME );
		}
	}

	public function __get( $key ) {
		return $this->get_meta( $key );
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
	 * to the taxonomy with the given $taxonomy_id
	 *
	 * @param int $taxonomy_id The ID of an existing post
	 * @return static
	 */
	public static function factory( $taxonomy_id ) {
		/** @var Meta_Repository $meta_repo */
		$meta_repo = apply_filters( Meta_Repository::GET_REPO_FILTER, NULL );
		if ( !$meta_repo ) {
			$meta_repo = new Meta_Repository();
		}
		$post = new static( $taxonomy_id, $meta_repo->get( static::NAME ) );
		return $post;
	}
}