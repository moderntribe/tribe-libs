# Tribe Libs Container

The Tribe Libs Container uses [PHP-DI](https://php-di.org/doc/) to help you build
[object graphs](https://en.wikipedia.org/wiki/Object_graph), allowing for autowiring
dependency injection.

## Mutable Container

In general, you should design your services to be [stateless](https://igor.io/2013/03/31/stateless-services.html).

However, for long-running processes (like queues) or in situations where you absolutely must create a fresh object, including all of its dependencies, you can use the Mutable Container.

This opens up the `makeFresh()` method, which works exactly like [PHP-DI's make() method](https://php-di.org/doc/container.html#make), but flushes the container's resolved entries, meaning the object's dependencies _will also be recreated from scratch_.

> NOTE: Only use this when necessary, this is not as performant as the default container.

In a definer:

```php

<?php declare(strict_types=1);

namespace Tribe\Project\Feature;

use Psr\Container\ContainerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Tribe\Explore_Navigation\Azure\Factories\Client_Factory;
use Tribe\Libs\Container\Definer_Interface;
use Tribe\Libs\Container\MutableContainer;
use Tribe\Libs\Container\Container;

class My_Feature_Definer implements Definer_Interface {

	public function define(): array {
		return [
		    // Define our abstract to the interface, placing our existing container inside the mutable container.
		    MutableContainer::class => static fn ( ContainerInterface $c ) => ( new Container() )->wrap( $c ), 
		];
	}

}

```

Then you can utilize the `makeFresh()` method to create purely fresh objects
each time it's called:

```php
<?php declare(strict_types=1);

namespace Tribe\Project\Feature;

use Tribe\Libs\Container\Abstract_Subscriber;
use Tribe\Libs\Container\MutableContainer;

class My_Queue_Factory {

    protected MutableContainer $container;
    
    public function __construct( MutableContainer $container ) {
        $this->container = $container;
    }
    
    /**
     * Make a fresh object.
     * 
     * @param string $class_name e.g. \Tribe\Project\Feature\SomeFeature::class
     * 
     * @return mixed|string
     */
    public function make( string $class_name ) {
        return $this->container->makeFresh( $class_name );
    }

}


```

Then call your factory:

```php
<?php declare(strict_types=1);

namespace Tribe\Project\Feature;

use Tribe\Libs\Container\Abstract_Subscriber;
use Tribe\Libs\Container\MutableContainer;

class My_Feature_Subscriber extends Abstract_Subscriber {

	public function register(): void {
		add_action( 'init', function(): void {
		    // Both of these will be completely separate instances,
		    // including any dependencies SomeFeature::class has.
		    var_dump( $this->container->get( MutableContainer::class )->make( '\Tribe\Project\Feature\SomeFeature::class' ) );
		    
		    var_dump( $this->container->get( MutableContainer::class )->make( '\Tribe\Project\Feature\SomeFeature::class' ) );
		    
		    // This will be the same instance as the last freshly made object
		    var_dump( $this->container->get( SomeFeature::class ) );		    
		}, 10, 0 );
	}

}

```
