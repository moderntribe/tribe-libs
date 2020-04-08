<?php
declare( strict_types=1 );

namespace Tribe\Libs\Blog_Copier;

use Psr\Container\ContainerInterface;
use Tribe\Libs\Blog_Copier\Strategies\File_Copy_Strategy;
use Tribe\Libs\Container\Subscriber_Interface;

class Blog_Copier_Subscriber implements Subscriber_Interface {
	public function register( ContainerInterface $container ): void {
		$this->post_type( $container );
		$this->admin( $container );
		$this->manager( $container );
		$this->file_copy( $container );
	}

	protected function post_type( ContainerInterface $container ): void {
		add_action( 'init', function () {
			register_post_type( Copy_Manager::POST_TYPE, [
				'public' => false,
			] );
		} );
	}

	/**
	 * @param ContainerInterface $container
	 */
	protected function admin( ContainerInterface $container ) {
		add_action( 'network_admin_menu', function () use ( $container ) {
			$container->get( Network_Admin_Screen::class )->register_screen();
		}, 10, 0 );
		add_action( 'network_admin_edit_' . Network_Admin_Screen::NAME, function () use ( $container ) {
			$container->get( Network_Admin_Screen::class )->handle_submission();
		}, 10, 0 );

	}

	/**
	 * @param ContainerInterface $container
	 *
	 * @return void
	 */
	protected function manager( ContainerInterface $container ) {
		add_action( 'tribe/project/copy-blog/copy', function ( Copy_Configuration $configuration ) use ( $container ) {
			$container->get( Copy_Manager::class )->initialize( $configuration );
		}, 10, 1 );
		add_action( Copy_Manager::TASK_COMPLETE_ACTION, function ( $completed_task, $args ) use ( $container ) {
			$container->get( Copy_Manager::class )->schedule_next_step( $completed_task, $args );
		}, 10, 2 );
	}


	/**
	 * @param ContainerInterface $container
	 *
	 * @return void
	 */
	protected function file_copy( ContainerInterface $container ) {
		add_action( 'tribe/project/copy-blog/copy-files', function ( $src, $dest ) use ( $container ) {
			$container->get( File_Copy_Strategy::class )->handle_copy( $src, $dest );
		}, 10, 2 );
	}
}
