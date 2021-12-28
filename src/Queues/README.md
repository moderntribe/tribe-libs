# Queues

The Queue library provides a utility for background processing of potentially slow,
time consuming, or network-dependent tasks.

## Configure queues with the MySQL Backend

Include [Queue_Definer](https://github.com/moderntribe/tribe-libs/blob/master/src/Queues/Queues_Definer.php), [Queue_Subscriber](https://github.com/moderntribe/tribe-libs/blob/master/src/Queues/Queues_Subscriber.php) and the [Mysql_Backend_Definer](https://github.com/moderntribe/tribe-libs/blob/master/src/Queues_Mysql/Mysql_Backend_Definer.php), [Mysql_Backend_Subscriber](https://github.com/moderntribe/tribe-libs/blob/master/src/Queues_Mysql/Mysql_Backend_Subscriber.php) in your [Core.php](https://github.com/moderntribe/square-one/blob/main/wp-content/plugins/core/src/Core.php), **ensuring to add the MySQL providers items _after_ the Queue providers.**

Run the following to create the queues database table:
```shell
wp s1 queues add-table
```

## Getting the Queue Object

While it is possible (and in rare cases appropriate) to have multiple queues, most often
a project will use a single default queue. Using the DI container, your class constructor
should receive a `\Tribe\Libs\Queues\Contracts\Queue`. Autowiring should take care of the
rest to give you an instance of the appropriate queue class with the configured backend.

```php
class Example_Class {
  private $queue;
  public function __construct( \Tribe\Libs\Queues\Contracts\Queue $queue ) {
    $this->queue = $queue;
  }
}
```

## Creating a task handler

If you are putting things into queue, it is very likely you will need to create a custom task handler.
To create a Task class, implement `Tribe\Project\Queues\Contracts\Task`.

The `handle( array $args )` method is required and must return `true` on success (the task is marked as complete
and removed from the queue), `false` on failure (the task is added back into the queue to try again).

The task handler instance is run in isolation, but still supports auto wiring and dependency injection.

Example Task:

```php

<?php declare(strict_types=1);

namespace Tribe\Project\Queues\Tasks;

use Tribe\Libs\Queues\Contracts\Task;
use Tribe\Project\Posts\Post_Fetcher;

/**
 * An example of Queue Task using dependency injection
 */
class Cache_Slow_Query implements Task {

	public const OPTION = 'tribe_latest_post_cache';

	/**
	 * Example dependency injection: This object will be
	 * the service responsible for handling complex logic
	 * for this task. Moving the logic to a service object
	 * allows that functionality to be shared outside
	 * this task in case something else needs to consume
	 * it.
	 */
	private Post_Fetcher $post_fetcher;

	/**
	 * @param \Tribe\Project\Posts\Post_Fetcher $post_fetcher This is a concrete class. PHP-DI knows to automatically
	 *                                                        inject the instance without any needed configuration.
	 */
	public function __construct( Post_Fetcher $post_fetcher ) {
		$this->post_fetcher = $post_fetcher;
	}

	/**
	 * @param array $args This variable populated with the dynamic data you set
	 *                    when the Queue Task is dispatched.
	 *
	 * @example          $queue->dispatch( Tribe\Project\Queues\Tasks\Cache_Slow_Query::class, [ 'my_custom_post_type' ] );
	 *
	 * @return bool
	 */
	public function handle( array $args ): bool {
		// Create the $post_type variable via unpacking of $args[0]
		[ $post_type ] = $args;

		/**
		 * Fetch the posts from some very long and intensive query.
		 *
		 * @var \WP_Query $query
		 */
		$query = $this->post_fetcher->get_latest_posts( $post_type );

		/*
		 * There are no posts, so return true to avoid placing this back in the queue to run
		 * again until posts are found.
		 *
		 * In this scenario, we'd rather just check that the next time this task is dispatched
		 * to the queue.
		 */
		if ( empty( $query->posts ) ) {
			return true;
		}

		/*
		 * If for some reason our option update doesn't work, it'll automatically be placed back in the
		 * queue to try again.
		 *
		 * Some other service will query this option to display the posts instead of running the
		 * massive query above in real time.
		 */
		return update_option( self::OPTION, $query->posts, false );
	}

}

```

## Adding a task to the queue

You dispatch tasks to the queue to indicate which class handles the task, and the arguments array to pass
to the `handle()` method. For example, ff your tasks requires a Post ID to complete its process, 
you can populate the `$args` variable during dispatch and the static value will be saved in the Queue
for when the task is processed. 

```php
add_action( 'save_post', function ( $post_id ): void {
	$queue->dispatch( My_Task::class, [
		(int) $post_id,
	] );
}, 10, 1 );
```


## Processing a queue

Using WP-CLI `wp s1 queues process <queue-name>`. This will process all items in the queue.

Using the system crontab, set up a job to run every 5 minutes to kick off the queue process. This
task will run for approximately five minutes, polling the queue every second for something to do
(and sitting idle if there is nothing). After the five-minute time limit, the process will gracefully
terminate.

You can customize the timelimit with the `--timelimit=<time in seconds>` option, e.g. to change from the default
`300` to `500` seconds:

```bash
wp s1 queues process default --timelimit=500
```

Along with the cron to process the queue, also set a cron to clean up old data from the queue.
`wp s1 queues cleanup <queue-name>`

In the event that WP CLI is not available (such as on older WP Engine stacks), then the queues can be processed on WP Cron.
Cron support is disabled by default, but is enabled by setting `WP_DISABLE_CRON` to false. **This is not the preferred way
of using Queues, but can be used when system level CLI access is not available.**

## Cron jobs
The queue should run on a cronjob for as long as the timelimit.

```shell
# Run for the default 300 seconds/5 minutes
*/5 * * * * wp --path=/path/to/wordpress s1 queues process default

# Clean up old queue tasks every 30 minutes
*/30 * * * * wp --path=/path/to/wordpress s1 queues cleanup default
```

## Creating additional queues

Add a queue by extending the `Tribe\Project\Queues\Contracts\Queue` class.

A queue class only requires the method `get_name()`. The class `DefaultQueue` is a good example.

To create a `Queue` object, inject a backend object. The DI container will automatically inject
the global backend when autowiring is used. But if you're using an additional queue, chances are
you are using it so that you can speak to a different backend.

## Built-in tasks
### Noop
A good task to test that you have a functional Queue, Noop mostly processes tasks correctly the first time.
Add whatever message you'd like to `$args['fake']`
ex: `$queue->dispatch( Noop::class, [ 'fake' => 'custom message' ] );`

### Email
Built in is a task for wp_mail(). To use it you'll need to add the following to your WP config:
`define( 'QUEUE_MAIL', true );`
You can also optionally define a default queue name with `QUEUE_MAIL_QUEUE_NAME`. If this value is not set, it will default to `default`.
To process the queued mail items `wp s1 queues process <queue-name>` with WP-CLI.

### Other CLI commands
`wp s1 queues add-tasks [--count=0]`
If you need to test a queue/backend are registered and functioning properly. By default this
creates a random (1-50) Noop tasks.  Noop fails on processing about 10% of the time so you can
also verify ack/nack is functioning as expected.

`wp s1 queues list`
Lists the registered queues and corresponding backends.
