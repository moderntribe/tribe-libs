<?php declare(strict_types=1);

namespace Tribe\Libs\Field_Models\Models;

use Tribe\Libs\Field_Models\Collections\Swatch_Collection;
use Tribe\Libs\Field_Models\Collections\User_Collection;
use Tribe\Libs\Field_Models\Field_Model;
use Tribe\Libs\Tests\Fixtures\Child_One_Model;
use Tribe\Libs\Tests\Fixtures\Child_Two_Model;
use Tribe\Libs\Tests\Fixtures\Collection_Model;
use Tribe\Libs\Tests\Fixtures\Parent_Array_Model;
use Tribe\Libs\Tests\Fixtures\Parent_Array_Multi_Level_Model;
use Tribe\Libs\Tests\Fixtures\Parent_Model;
use Tribe\Libs\Tests\Test_Case;
use Tribe\Libs\Tests\Fixtures\Title_Model;
use stdClass;

final class AcfFieldModelsTest extends Test_Case {

	public function test_link_field(): void {
		$post_id = $this->factory()->post->create( [
			'post_status' => 'publish',
		] );

		$field_key = 'field_test_link';

		acf_add_local_field( [
			'key'           => $field_key,
			'name'          => 'test_link',
			'type'          => 'link',
			'return_format' => 'array',
		] );

		$data = [
			'title'  => $this->faker->title(),
			'url'    => $this->faker->url(),
			'target' => '_blank',
		];

		$this->assertNotEmpty( update_field( $field_key, $data, $post_id ) );

		$link = new Link( get_field( $field_key, $post_id ) );

		$this->assertSame( $data, $link->toArray() );
	}

	public function test_swatch_field_choices(): void {
		$swatches = [
			'white' => [
				'color' => '#ffffff',
				'name' => __( 'White', 'tribe' ),
				'slug'  => 'white',
			],
			'black' => [
				'color' => '#000000',
				'name' => __( 'Black', 'tribe' ),
				'slug'  => 'black',
			],
			'grey' => [
				'color' => '#696969',
				'name' => __( 'Grey', 'tribe' ),
				'slug'  => 'grey',
			],
		];

		$collection = Swatch_Collection::create( $swatches );

		$field_key = 'field_test_swatch';

		acf_add_local_field( [
			'key'           => $field_key,
			'name'          => 'test_swatch',
			'type'          => 'swatch',
			'choices'       => $collection->format_for_acf(),
		] );

		$field = get_field_object( $field_key );

		$this->assertCount( 3, $field['choices'] );

		$this->assertSame( 'White', $field['choices']['#ffffff'] );
		$this->assertSame( 'Black', $field['choices']['#000000'] );
		$this->assertSame( 'Grey', $field['choices']['#696969'] );
	}

	public function test_file_field(): void {
		$post_id = $this->factory()->post->create( [
			'post_status' => 'publish',
		] );

		$image     = codecept_data_dir( 'test.jpg' );
		$post_date = date( 'Y-m-d H:i:s', strtotime( 'now' ) );

		$attachment_id = $this->factory()->attachment->create( [
			'file'           => $image,
			'post_title'     => 'Test image',
			'post_parent'    => $post_id,
			'post_content'   => 'This is a test image description',
			'post_excerpt'   => 'This is a test image caption',
			'post_mime_type' => 'image/jpeg',
			'post_date'      => $post_date,
			'post_modified'  => $post_date,
		] );

		$field_key = 'field_test_file';

		acf_add_local_field( [
			'key'           => $field_key,
			'name'          => 'test_file',
			'type'          => 'file',
			'return_format' => 'array',
		] );

		$this->assertNotEmpty( update_field( $field_key, $attachment_id, $post_id ) );

		$file = new File( get_field( $field_key, $post_id ) );

		$this->assertSame( $attachment_id, $file->id );

		$attachment_data = $this->get_acf_attachment_data( $attachment_id );
		$this->assertNotEmpty( $attachment_data );

		$this->assertEquals( $attachment_data, $file->toArray() );
	}

	public function test_image_field(): void {
		$post_id = $this->factory()->post->create( [
			'post_status' => 'publish',
		] );

		$image_file     = codecept_data_dir( 'test.jpg' );
		$post_date = date( 'Y-m-d H:i:s', strtotime( 'now' ) );

		$attachment_id = $this->factory()->attachment->create( [
			'file'           => $image_file,
			'post_title'     => 'Test image',
			'post_parent'    => $post_id,
			'post_content'   => 'This is a test image description',
			'post_excerpt'   => 'This is a test image caption',
			'post_mime_type' => 'image/jpeg',
			'post_date'      => $post_date,
			'post_modified'  => $post_date,
		] );

		$field_key = 'field_test_image';

		acf_add_local_field( [
			'key'           => $field_key,
			'name'          => 'test_image',
			'type'          => 'image',
			'return_format' => 'array',
		] );

		$this->assertNotEmpty( update_field( $field_key, $attachment_id, $post_id ) );

		$image = new Image( get_field( $field_key, $post_id ) );

		$this->assertSame( $attachment_id, $image->id );

		$attachment_data = $this->get_acf_attachment_data( $attachment_id );
		$this->assertNotEmpty( $attachment_data );

		$this->assertEquals( $attachment_data, $image->toArray() );
	}

	public function test_user_field(): void {
		$user_id = $this->factory()->user->create();
		$post_id = $this->factory()->post->create( [
			'post_status' => 'publish',
		] );

		$field_key = 'field_test_user';

		acf_add_local_field( [
			'key'           => $field_key,
			'name'          => 'test_user',
			'type'          => 'user',
			'return_format' => 'array',
		] );

		$this->assertNotEmpty( update_field( $field_key, $user_id, $post_id ) );

		$user = new User( get_field( $field_key, $post_id ) );

		$wp_user = get_user_by( 'ID', $user_id );

		$this->assertSame( $user_id, $user->ID );

		$this->assertSame( [
			'ID'               => $wp_user->ID,
			'user_firstname'   => $wp_user->user_firstname,
			'user_lastname'    => $wp_user->user_lastname,
			'nickname'         => $wp_user->nickname,
			'user_nicename'    => $wp_user->user_nicename,
			'display_name'     => $wp_user->display_name,
			'user_email'       => $wp_user->user_email,
			'user_url'         => $wp_user->user_url,
			'user_registered'  => $wp_user->user_registered,
			'user_description' => $wp_user->user_description,
			'user_avatar'      => get_avatar( $wp_user->ID ),
		], $user->toArray() );

	}

	public function test_cta_field(): void {
		$post_id = $this->factory()->post->create( [
			'post_status' => 'publish',
		] );

		$field_key = 'field_test_cta';

		acf_add_local_field( [
			'key'           => $field_key,
			'name'          => 'test_cta',
			'type'          => 'link',
			'return_format' => 'array',
		] );

		$data = [
			'title'  => $this->faker->title(),
			'url'    => $this->faker->url(),
			'target' => '_blank',
		];

		$this->assertNotEmpty( update_field( $field_key, $data, $post_id ) );

		$link = new Cta( [
			'link'           => get_field( $field_key, $post_id ),
			'add_aria_label' => true,
			'aria_label'     => 'Screen reader text',
		] );

		$this->assertSame( [
			'link'           => $data,
			'add_aria_label' => true,
			'aria_label'     => 'Screen reader text',
		], $link->toArray() );
	}

	public function test_cta_field_with_different_submission_structures(): void {
		$post_id = $this->factory()->post->create( [
			'post_status' => 'publish',
		] );

		$field_key = 'field_test_cta';

		acf_add_local_field( [
			'key'           => $field_key,
			'name'          => 'test_cta',
			'type'          => 'link',
			'return_format' => 'array',
		] );

		$empty_field_value = get_field( $field_key, $post_id );

		// Random ACF return type
		$this->assertNull( $empty_field_value );
		$cta = new Cta( [ 'link' => $empty_field_value ] );

		$this->assertInstanceOf( Field_Model::class, $cta );
		$this->assertEquals( new Link(), $cta->link );

		// boolean
		$cta2 = new Cta( [ 'link' => false ] );
		$this->assertInstanceOf( Field_Model::class, $cta2 );
		$this->assertEquals( new Link(), $cta2->link );

		// empty array
		$cta3 = new Cta( [ 'link' => [] ] );
		$this->assertInstanceOf( Field_Model::class, $cta3 );
		$this->assertEquals( new Link(), $cta3->link );

		// completely missing child definition
		$cta4 = new Cta();
		$this->assertInstanceOf( Field_Model::class, $cta4 );
		$this->assertEquals( new Link(), $cta4->link );

		// invalid and partial data
		$cta5 = new Cta( [
			'add_aria_label' => 'this should be a boolean',
			'link'           => [
				0               => 'mixed array',
				'invalid_index' => 'I do not exist',
				'url'           => 'https://tri.be',
			],
		] );
		$this->assertInstanceOf( Field_Model::class, $cta5 );
		$this->assertSame( [
			'title'  => '',
			'url'    => 'https://tri.be',
			'target' => '_self',
		], $cta5->link->toArray() );

		// PHP converted this into true through type juggling of the 'this should be a boolean' string.
		$this->assertTrue( $cta5->add_aria_label );

		// already created child instance
		$cta6 = new Cta( [
			'link'           => new Link( [
				'title'  => 'Click me',
				'url'    => 'https://tri.be',
				'target' => '_blank',
			] ),
			'add_aria_label' => true,
			'aria_label'     => 'Screen reader text',
		] );
		$this->assertInstanceOf( Field_Model::class, $cta6 );
		$this->assertEquals( [
			'title'  => 'Click me',
			'url'    => 'https://tri.be',
			'target' => '_blank',
		], $cta6->link->toArray() );

		// raw child object
		$cta7 = new Cta( [
			'link'           => (object) [
				'title'  => 'Click me',
				'url'    => 'https://tri.be',
				'target' => '_blank',
			],
			'add_aria_label' => true,
			'aria_label'     => 'Screen reader text',
		] );
		$this->assertInstanceOf( Field_Model::class, $cta7 );
		$this->assertEquals( [
			'title'  => 'Click me',
			'url'    => 'https://tri.be',
			'target' => '_blank',
		], $cta7->link->toArray() );

		// Blank object in place of an array
		$cta8 = new Cta( [
			'link'           => new stdClass(),
			'add_aria_label' => true,
			'aria_label'     => 'Screen reader text',
		] );
		$this->assertInstanceOf( Field_Model::class, $cta8 );
		$this->assertEquals( [
			'title'  => '',
			'url'    => '',
			'target' => '_self', // the default
		], $cta8->link->toArray() );
	}

	public function test_nested_models(): void {
		$data = [
			'name'      => 'parent',
			'child_one' => [
				'name'      => 'Child One',
				'child_two' => [
					'name' => 'Child Two',
				],
			],
		];

		$model = new Parent_Model( $data );

		$this->assertEquals( $data, $model->toArray() );
		$this->assertInstanceOf( Child_One_Model::class, $model->child_one );
		$this->assertInstanceOf( Child_Two_Model::class, $model->child_one->child_two );
	}

	public function test_empty_nested_models(): void {
		$data = [
			'name' => 'parent',
		];

		$model = new Parent_Model( $data );

		$this->assertNotEquals( $data, $model->toArray() );
		$this->assertSame( 'parent', $model->name );
		$this->assertInstanceOf( Child_One_Model::class, $model->child_one );
		$this->assertInstanceOf( Child_Two_Model::class, $model->child_one->child_two );
		$this->assertSame( '', $model->child_one->name );
		$this->assertSame( 'This is my default', $model->child_one->child_two->name );
	}

	public function test_array_to_model_casting_one_level_deep(): void {
		$data = [
			'children' => [
				[
					'name' => 'Child 0',
				],
				[
					'name' => 'Child 1',
				],
				[
					'name' => 'Child 2',
				],
			],
		];

		$model = new Parent_Array_Model( $data );

		foreach ( $model->children as $key => $child ) {
			$this->assertInstanceOf( Child_Two_Model::class, $child );
			$this->assertSame( sprintf( 'Child %d', $key ), $child->name );
		}
	}

	public function test_array_to_model_casting_multiple_level_deep(): void {
		$data = [
			'parents' => [
				[
					'children' => [
						[
							'name' => 'Child 0',
						],
						[
							'name' => 'Child 1',
						],
						[
							'name' => 'Child 2',
						],
					],
				],
				[
					'children' => [
						[
							'name' => 'Child 0',
						],
						[
							'name' => 'Child 1',
						],
						[
							'name' => 'Child 2',
						],
					],
				],
			],
			'titles' => [
				[
					'name' => 'Title 1',
				],
				[
					'name' => 'Title 2',
					'tag'  => 'h2',
				],
				[
					'name' => 'Title 3',
					'tag'  => 'h3',
				],
			],
		];

		$model = new Parent_Array_Multi_Level_Model( $data );

		$this->assertCount( 2, $model->parents );

		foreach ( $model->parents as $child ) {

			$this->assertInstanceOf( Parent_Array_Model::class, $child );
			$this->assertCount( 3, $child->children );

			foreach ( $child->children as $key => $grand_child ) {
				$this->assertInstanceOf( Child_Two_Model::class, $grand_child );
				$this->assertSame( sprintf( 'Child %d', $key ), $grand_child->name );
			}
		}

		$this->assertCount( 3, $model->titles );

		foreach ( $model->titles as $key => $title ) {
			$k = ++$key;
			$this->assertInstanceOf( Title_Model::class, $title );
			$this->assertSame( sprintf( 'Title %d', $k ), $title->name );
			$this->assertSame( sprintf( 'h%d', $k ), $title->tag );
		}
	}

	public function test_array_to_model_casting_with_missing_values(): void {
		$model = new Parent_Array_Model( [] );

		$this->assertIsArray( $model->children );
		$this->assertEmpty( $model->children );

		$model = new Parent_Array_Model( [
			'children' => false,
		] );

		$this->assertIsArray( $model->children );
		$this->assertEmpty( $model->children );

		$model = new Parent_Array_Model( [
			'children' => '',
		] );

		$this->assertIsArray( $model->children );
		$this->assertEmpty( $model->children );
	}

	public function test_collection_casting(): void {
		$model = new Collection_Model( [
			'users' => [
				[
					'ID'             => 1,
					'user_firstname' => 'User 1',
					'user_email'     => 'user1@test.com',
				],
				[
					'ID'             => 2,
					'user_firstname' => 'User 2',
					'user_email'     => 'user2@test.com',
				],
				[
					'ID'             => 3,
					'user_firstname' => 'User 3',
					'user_email'     => 'user3@test.com',
				],
			],
		] );

		$this->assertInstanceOf( User_Collection::class, $model->users );
		$this->assertCount( 3, $model->users );

		foreach ( $model->users as $key => $user ) {
			$k = ++$key;
			$this->assertSame( $k, $user->ID );
			$this->assertSame( sprintf( 'User %d', $k ), $user->user_firstname );
			$this->assertSame( sprintf( 'user%d@test.com', $k ), $user->user_email );
		}

		$model = new Collection_Model( [] );

		$this->assertInstanceOf( User_Collection::class, $model->users );
		$this->assertCount( 0, $model->users );

		$model = new Collection_Model( [
			'users' => false,
		] );

		$this->assertInstanceOf( User_Collection::class, $model->users );
		$this->assertCount( 0, $model->users );

		$model = new Collection_Model( [
			'users' => '',
		] );

		$this->assertInstanceOf( User_Collection::class, $model->users );
		$this->assertCount( 0, $model->users );
	}

	/**
	 * The attachment data that ACF creates, we'll use it to compare to
	 * our field models.
	 *
	 * @param  int  $attachment_id
	 *
	 * @return array<string, mixed>
	 */
	private function get_acf_attachment_data( int $attachment_id ): array {
		$data = acf_get_attachment( $attachment_id );

		// Remove the duplicate ID. ACF has both ID and id, we just need one.
		unset( $data['ID'] );

		return $data;
	}

}
