<?php


namespace Tribe\Libs\Blog_Copier;


use Pimple\Container;
use Tribe\Libs\Blog_Copier\Strategies\Shell_File_Copy;
use Tribe\Libs\Blog_Copier\Tasks\Cleanup;
use Tribe\Libs\Blog_Copier\Tasks\Copy_Files;
use Tribe\Libs\Blog_Copier\Tasks\Create_Blog;
use Tribe\Libs\Blog_Copier\Tasks\Mark_Complete;
use Tribe\Libs\Blog_Copier\Tasks\Replace_Guids;
use Tribe\Libs\Blog_Copier\Tasks\Replace_Options;
use Tribe\Libs\Blog_Copier\Tasks\Replace_Tables;
use Tribe\Libs\Blog_Copier\Tasks\Replace_Urls;
use Tribe\Libs\Blog_Copier\Tasks\Send_Notifications;
use Tribe\Libs\Container\Service_Provider;
use Tribe\Libs\Queues\Queues_Provider;

class Blog_Copier_Provider extends Service_Provider {
	const ADMIN_SCREEN       = 'blog_copier.admin_screen';
	const MANAGER            = 'blog_copier.manager';
	const CHAIN              = 'blog_copier.chain';
	const FILE_COPY_STRATEGY = 'blog_copier.file_copy_strategy';

	public function register( Container $container ) {
		$this->admin( $container );
		$this->tasks( $container );
		$this->manager( $container );
		$this->file_copy( $container );
	}

	/**
	 * @param Container $container
	 */
	protected function admin( Container $container ) {
		$container[ self::ADMIN_SCREEN ] = function ( Container $container ) {
			return new Network_Admin_Screen();
		};

		add_action( 'network_admin_menu', function () use ( $container ) {
			$container[ self::ADMIN_SCREEN ]->register_screen();
		}, 10, 0 );
		add_action( 'network_admin_edit_' . Network_Admin_Screen::NAME, function () use ( $container ) {
			$container[ self::ADMIN_SCREEN ]->handle_submission();
		}, 10, 0 );

	}

	/**
	 * @param Container $container
	 *
	 * @return void
	 */
	protected function tasks( Container $container ) {
		$container[ self::CHAIN ] = function ( Container $container ) {
			return new Task_Chain( [
				Create_Blog::class,
				Replace_Tables::class,
				Replace_Options::class,
				Replace_Guids::class,
				Copy_Files::class,
				Replace_Urls::class,
				Mark_Complete::class,
				Send_Notifications::class,
				Cleanup::class,
			] );
		};
	}

	/**
	 * @param Container $container
	 *
	 * @return void
	 */
	protected function manager( Container $container ) {
		$container[ self::MANAGER ] = function ( Container $container ) {
			return new Copy_Manager( $container[ Queues_Provider::DEFAULT_QUEUE ], $container[ self::CHAIN ] );
		};

		add_action( 'tribe/project/copy-blog/copy', function ( Copy_Configuration $configuration ) use ( $container ) {
			$container[ self::MANAGER ]->initialize( $configuration );
		}, 10, 1 );
		add_action( Copy_Manager::TASK_COMPLETE_ACTION, function ( $completed_task, $args ) use ( $container ) {
			$container[ self::MANAGER ]->schedule_next_step( $completed_task, $args );
		}, 10, 2 );
	}


	/**
	 * @param Container $container
	 *
	 * @return void
	 */
	protected function file_copy( Container $container ) {
		$container[ self::FILE_COPY_STRATEGY ] = function ( Container $container ) {
			return new Shell_File_Copy();

			// If the hosting environment does not support exec(), use this instead:
			// return new Recursive_File_Copy();
		};

		add_action( 'tribe/project/copy-blog/copy-files', function ( $src, $dest ) use ( $container ) {
			$container[ self::FILE_COPY_STRATEGY ]->handle_copy( $src, $dest );
		}, 10, 2 );
	}
}