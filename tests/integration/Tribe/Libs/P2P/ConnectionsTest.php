<?php declare(strict_types=1);

namespace Tribe\Libs\P2P;

use Tribe\Libs\Tests\Test_Case;

/**
 * Class ConnectionsTest
 *
 * @package Tribe\Project\P2P
 */
final class ConnectionsTest extends Test_Case {

	private const META_KEY = 'test_meta_key';
	private const META_VALUE = 'test meta value';
	private const SAMPLE_TO_POST = 'Sample_To_Post';

	private $connection_id;
	private $connection_2_id;
	private $connection_3_id;
	private $sample_id;
	private $page_id;
	private $page_2_id;
	private $post_id;

	public function setUp(): void {
		parent::setUp();

		$this->sample_id = self::factory()->post->create( [
			'post_title' => 'Test Sample Post',
			'post_type' => 'sample',
		] );

		$this->page_id = self::factory()->post->create( [
			'post_title' => 'Test Page',
			'post_type' => 'page',
		] );

		$this->page_2_id = self::factory()->post->create( [
			'post_title' => 'Test Page Two With Meta',
			'post_type' => 'page',
		] );

		$this->post_id = self::factory()->post->create( [
			'post_title' => 'Test Post',
			'post_type' => 'post'
		] );

		p2p_register_connection_type( $this->get_all_args() );

		add_post_meta( $this->page_2_id, self::META_KEY, self::META_VALUE );

		$connect = [ 'from' => $this->sample_id, 'to' => $this->page_id ];
		$this->connection_id = p2p_create_connection( 'Sample_To_Page', $connect );
		$connect = [ 'from' => $this->sample_id, 'to' => $this->page_2_id ];
		$this->connection_2_id = p2p_create_connection( 'Sample_To_Page', $connect );
		$connect = [ 'from' => $this->sample_id, 'to' => $this->post_id ];
		$this->connection_3_id = p2p_create_connection( self::SAMPLE_TO_POST, $connect );

		p2p_add_meta( $this->connection_2_id, self::META_KEY, self::META_VALUE );
	}

	public function tearDown(): void {
		// your tear down methods here

		parent::tearDown();
	}

	/**
	 * Test retrieving connections of specific type and from post id
	 */
	public function test_get_connection() {
		$connections = new Connections();
		$ids = $connections->get_from(
			$this->sample_id,
			[
				'type' => [
					'Sample_To_Page',
				],
			]
		);

		$this->assertTrue( count( $ids ) === 2 );
		$this->assertTrue( $this->page_id === $ids[0] );
		$this->assertTrue( $this->page_2_id === $ids[1] );
	}

	/**
	 * Test getting id's from p2p with requirements on post meta
	 */
	public function test_get_meta_connection() {
		$connections = new Connections();
		$ids = $connections->get_from(
			$this->sample_id,
			[
				'type' => [
					'Sample_To_Page',
				],
				'meta' => [
					'key' => self::META_KEY,
				],
			]
		);

		$this->assertTrue( $this->page_2_id === $ids[0] );
	}

	/**
	 * Test getting connections based on p2p meta
	 */
	public function test_get_connections_by_p2p_meta() {
		$connections = new Connections();
		$results = $connections->get_connections_by_p2p_meta( self::META_KEY );

		$this->assertTrue( $this->connection_2_id === (int) $results[0]->p2p_id );

		$results = $connections->get_connections_by_p2p_meta( self::META_KEY, self::META_VALUE );

		$this->assertTrue( $this->connection_2_id === (int) $results[0]->p2p_id );

		$results = $connections->get_connections_by_p2p_meta( self::META_KEY, 'not a meta value' );
		$this->assertEmpty( $results );
	}

	/**
	 * Test getting shared connected items of different connection types
	 */
	public function test_get_shared_connections() {
		$connections = new Connections();

		$results = $connections->get_shared_connections( $this->sample_id );
		$to_ids = wp_list_pluck( $results, 'p2p_to' );
		$to_ids = array_flip( $to_ids );
		$this->assertArrayHasKey( $this->page_id, $to_ids );
		$this->assertArrayHasKey( $this->post_id, $to_ids );

		$results = $connections->get_shared_connections( $this->sample_id, [ self::SAMPLE_TO_POST ] );
		$to_ids = wp_list_pluck( $results, 'p2p_to' );
		$to_ids = array_flip( $to_ids );
		$this->assertArrayNotHasKey( $this->page_id, $to_ids );
		$this->assertArrayHasKey( $this->post_id, $to_ids );
	}

	private function get_all_args() {
		return [
			'id' => self::SAMPLE_TO_POST,
			'from' => 'sample',
			'to' => 'post',
			'reciprocal'      => true,
			'cardinality'     => 'many-to-many',
			'admin_box'       => [
				'show'    => 'any',
				'context' => 'side',
			],
			'title'           => [
				'from' => __( 'Related Posts', 'tribe' ),
				'to'   => __( 'Related Samples', 'tribe' ),
			],
			'from_labels'     => [
				'singular_name' => __( 'Sample', 'tribe' ),
				'search_items'  => __( 'Search', 'tribe' ),
				'not_found'     => __( 'Nothing found.', 'tribe' ),
				'create'        => __( 'Relate Sample', 'tribe' ),
			],
			'to_labels'       => [
				'singular_name' => __( 'Post', 'tribe' ),
				'search_items'  => __( 'Search', 'tribe' ),
				'not_found'     => __( 'Nothing found.', 'tribe' ),
				'create'        => __( 'Relate Post', 'tribe' ),
			],
			'can_create_post' => false,
		];
	}

}
