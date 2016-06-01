<?php


namespace Tribe\Libs\Post_Meta;


/**
 * Class Meta_Repository
 *
 * A global repository of Meta_Map and Meta_Group objects
 */
class Meta_Repository {
	/**
	 * The filter that will run to get the global Meta_Repository
	 */
	const GET_REPO_FILTER = 'tribe_get_meta_repo';

	/** @var Meta_Group[] */
	private $groups = [ ];

	/** @var Meta_Map[] */
	private $collections = [ ];

	/**
	 * Meta_Repository constructor.
	 *
	 * @param Meta_Group[] $meta Initial meta groups for this collection
	 */
	public function __construct( $meta = [ ] ) {
		foreach ( $meta as $group ) {
			$this->add_group( $group );
		}
	}

	/**
	 * Hook this repository and its meta groups into WP
	 */
	public function hook() {
		foreach ( $this->groups as $group ) {
			$group->hook();
		}
		add_filter( self::GET_REPO_FILTER, [ $this, 'filter_global_instance' ], 10, 1 );
	}

	/**
	 * Assuming this is hooked in, declares itself as the global Meta_Repository
	 * 
	 * @param mixed $repo
	 * @return $this
	 */
	public function filter_global_instance( $repo ) {
		return $this;
	}

	/**
	 * @param Meta_Group $group
	 * @return void
	 */
	public function add_group( Meta_Group $group ) {
		$this->groups[ $group::NAME ] = $group;
		foreach ( $group->get_post_types() as $post_type ) {
			$this->get( $post_type )->add( $group );
		}
	}

	/**
	 * Set/override the Meta_Map for the given post type
	 *
	 * @param Meta_Map $collection
	 * @param string   $post_type
	 * @return void
	 */
	public function set( Meta_Map $collection, $post_type ) {
		$this->collections[ $post_type ] = $collection;
	}

	/**
	 * @param string $post_type
	 * @return Meta_Map The meta collection relevant to the given post type
	 */
	public function get( $post_type ) {
		if ( !isset( $this->collections[ $post_type ] ) ) {
			$this->set( new Meta_Map( $post_type ), $post_type );
		}
		return $this->collections[ $post_type ];
	}
}