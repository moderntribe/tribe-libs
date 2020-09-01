# Queues

The Queue library provides a utility for background processing of potentially slow,
time consuming, or network-dependent tasks.

## Getting the Queue Object

While it is possible (and in rare cases appropriate) to have multiple queues, most often
a project will use a single default queue. Using the DI container, your class constructor
should receive a `\Tribe\Libs\Queues\Contracts\Queue`. Autowiring should take care of the
reset to give you an instance of the appropriate queue class with the configured backend.

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
To create a Task class implement `Tribe\Project\Queues\Contracts\Task`.

The method `handle()` is required and must return `true` on success (the task is marked as complete
and removed from the queue), `false` on failure (the task is added back into the queue to try again).

## Adding a task to the queue

You dispatch tasks to the queue to indicate which class handles the task, and the arguments array to pass
to the `handle()` method.

```php
$queue->dispatch( Task::class, $args );
```


## Processing a queue

Using WP-CLI `wp s1 queues process <queue-name>`. This will process all items in the queue.

Using the system crontab, set up a job to run every 5 minutes to kick off the queue process. This
task will run for approximately five minutes, polling the queue every second for something to do
(and sitting idle if there is nothing). After the five-minute time limit, the process will gracefully
terminate.

Along with the cron to process the queue, also set a cron to clean up old data from the queue.
`wp s1 queues cleanup <queue-name>`

In the event that WP CLI is not available (such as on WP Engine), then the queues can be processed on WP Cron.
Cron support is disabled by default, but is enabled by setting `WP_DISABLE_CRON` to false. **This is not the preferred way
of using Queues, but can be used when system level CLI access is not available.**

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
