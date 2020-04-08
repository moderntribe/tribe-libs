<?php
declare( strict_types=1 );

namespace Tribe\Libs\Blog_Copier;

use DI;
use Tribe\Libs\Blog_Copier\Strategies\File_Copy_Strategy;
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
use Tribe\Libs\Container\Definer_Interface;
use Tribe\Libs\Queues\Contracts\Queue;

class Blog_Copier_Definer implements Definer_Interface {
	public const TASK_CHAIN = 'blog_copier.task_chain';

	public function define(): array {
		return [
			Copy_Manager::class => DI\autowire()
				->constructor( DI\get( Queue::class ), DI\get( self::TASK_CHAIN ) ),

			self::TASK_CHAIN => function () {
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
			},

			File_Copy_Strategy::class => DI\autowire( Shell_File_Copy::class ),
			// If the hosting environment does not support exec(), override to use this instead:
			// File_Copy_Strategy::class => DI\autowire( Recursive_File_Copy::class ),
		];
	}
}
