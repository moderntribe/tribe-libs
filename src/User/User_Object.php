<?php declare(strict_types=1);

namespace Tribe\Libs\User;

use Tribe\Libs\Object_Meta\Meta_Map;
use Tribe\Libs\Object_Meta\Meta_Repository;

/**
 * Class User_Object
 *
 * For most not-test usage, instances of this class
 * will be created via the `factory()` method called
 * on the subclasses.
 *
 * Instances can be used to access meta registered by
 * an appropriate Meta_Group, via the `get_meta()` method
 * called with a registered key.
 */
class User_Object {

	public const NAME = 'users';

	protected Meta_Map $meta;

	protected int $user_id = 0;

	/**
	 * User_Object constructor.
	 *
	 * @param int           $user_id        The ID of a User.
	 * @param \Tribe\Libs\Object_Meta\Meta_Map|null $meta Meta fields appropriate to a user.
	 */
	public function __construct( int $user_id = 0, ?Meta_Map $meta = null ) {
		$this->user_id = $user_id;
		if ( isset( $meta ) ) {
			$this->meta = $meta;
		} else {
			$this->meta = new Meta_Map( self::NAME );
		}
	}

	/**
	 * Get an instance of the User_Object corresponding
	 * to the user with the given $user_id
	 *
	 * @param int $user_id The ID of an existing user
	 *
	 * @return static
	 */
	public static function factory( int $user_id ): self {
		/** @var \Tribe\Libs\Object_Meta\Meta_Repository|null $meta_repo */
		$meta_repo = apply_filters( Meta_Repository::GET_REPO_FILTER, null );

		if ( empty( $meta_repo ) ) {
			$meta_repo = new Meta_Repository();
		}

		return new static( $user_id, $meta_repo->get( self::NAME ) );
	}

	/**
	 * Get the value for the given meta key corresponding
	 * to this user.
	 *
	 * @param string $key
	 *
	 * @return mixed
	 */
	public function get_meta( string $key ) {
		$user = sprintf( 'user_%s', $this->user_id );

		return $this->meta->get_value( $user, $key );
	}

	/**
	 * @param string $key
	 *
	 * @return mixed
	 */
	public function __get( string $key ) {
		return $this->get_meta( $key );
	}

}
