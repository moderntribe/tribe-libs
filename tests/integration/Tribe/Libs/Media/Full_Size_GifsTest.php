<?php

namespace Tribe\Libs\Media;


/**
 * Class Full_Size_GifsTest
 */
class Full_Size_GifsTest extends \Codeception\TestCase\WPTestCase {

	public function test_full_size_only_gif_src() {
		// set up our filter
		$full_size_gif = new Full_Size_Gif();
		add_filter( 'image_downsize', [ $full_size_gif, 'full_size_only_gif' ], 10, 3 );

		// create the test file
		$filename       = codecept_data_dir( 'test.gif' );
		$parent_post_id = wp_insert_post( [ 'title' => 'test_gifs', 'post_type' => 'post' ] );
		$attachment_id = self::factory()->attachment->create_upload_object( $filename, $parent_post_id );

		// get the full and "resized" images
		$full = wp_get_attachment_image_src( $attachment_id, 'full' );
		$thumb = wp_get_attachment_image_src( $attachment_id, 'thumbnail' );

		// clean up
		wp_delete_attachment( $attachment_id, true );
		remove_filter( 'image_downsize', [ $full_size_gif, 'full_size_only_gif' ], 10 );

		$this->assertEqualSets( $full, $thumb );
	}

}
