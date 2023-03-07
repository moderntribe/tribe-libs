<?php declare(strict_types=1);

namespace Tribe\Libs\Field_Models\Models;

use Tribe\Libs\Field_Models\Field_Model;
use WP_Post;

/**
 * Proxy object to add additional data on top of WP_Post and proxy
 * property requests to the underlying WP_Post object.
 *
 * @property-read int[]    $post_category
 *
 * @mixin \WP_Post
 */
class Post_Proxy extends Field_Model {

	public Cta $cta;
	public Image $image;
	private WP_Post $post;

	public function __construct( array $parameters = [] ) {
		$this->post = new WP_Post( (object) $parameters );

		parent::__construct( $parameters );
	}

	/**
	 * Return the delegated WP_Post object.
	 *
	 * @return \WP_Post
	 */
	public function post(): WP_Post {
		return $this->post;
	}

	/**
	 * Whether this post exists in the database or not. Faux
	 * posts have negative ID's.
	 *
	 * @return bool
	 */
	public function is_faux_post(): bool {
		return $this->post()->ID < 0;
	}

	/**
	 * Proxy properties to the WP_Post delegate.
	 *
	 * @param string $property
	 *
	 * @return array|mixed|null
	 */
	public function __get( string $property ) {
		return $this->post->{$property};
	}

}
