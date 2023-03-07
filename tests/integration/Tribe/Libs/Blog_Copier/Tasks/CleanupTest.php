<?php declare(strict_types=1);

namespace Tribe\Libs\Blog_Copier\Tasks;

use Tribe\Libs\Blog_Copier\Copy_Manager;
use Tribe\Libs\Tests\Test_Case;

final class CleanupTest extends Test_Case {

	public function test_deletes_post() {
		$post_id = $this->factory()->post->create( [
			'post_type'   => Copy_Manager::POST_TYPE,
			'post_status' => 'publish',
		] );

		$this->assertCount( 1, get_posts( [
			'post_type' => Copy_Manager::POST_TYPE,
			'p'         => $post_id,
		] ) );

		$cleanup = new Cleanup();
		$cleanup->handle( [
			'post_id' => $post_id,
		] );

		$this->assertCount( 0, get_posts( [
			'post_type' => Copy_Manager::POST_TYPE,
			'p'         => $post_id,
		] ) );
	}

}
