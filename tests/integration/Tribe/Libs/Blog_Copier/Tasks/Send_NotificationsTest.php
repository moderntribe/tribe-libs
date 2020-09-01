<?php

namespace Tribe\Libs\Blog_Copier\Tasks;

use Tribe\Libs\Blog_Copier\Copy_Configuration;
use Tribe\Libs\Blog_Copier\Copy_Manager;

class Send_NotificationsTest extends \Codeception\TestCase\WPTestCase {

	public function test_sends_emails() {
		/** @var \WP_User $user */
		$user    = $this->factory()->user->create_and_get();
		$config  = new Copy_Configuration( [
			'src'     => 2,
			'address' => 'dest',
			'title'   => 'Copy Destination',
			'files'   => true,
			'notify'  => 'alpha@example.com,beta@example.com',
			'user'    => $user->ID,
		] );
		$post_id = $this->factory()->post->create( [
			'post_type'    => Copy_Manager::POST_TYPE,
			'post_status'  => 'publish',
			'post_content' => \json_encode( $config ),
		] );

		update_post_meta( $post_id, Copy_Manager::DESTINATION_BLOG, 3 );

		$task = new Send_Notifications();
		$task->handle( [
			'post_id' => $post_id,
		] );

		/** @var \MockPHPMailer $mailer */
		$mailer = tests_retrieve_phpmailer_instance();

		$this->assertEqualSets( [ 'alpha@example.com', 'beta@example.com' ], array_column( $mailer->getToAddresses(), 0 ) );
		$this->assertEquals( 'Blog Copy Complete', $mailer->Subject );
		$this->assertStringContainsString( $config->get_title(), $mailer->Body );
	}
}
