# Required Pages

This utility makes it convenient to register arbitrary content pages
that will be automatically created, and automatically regenerated in
the event that an editor deletes them.

## Usage

Create a class that extends `Required_Page`:

```php
<?php

namespace Tribe\Example\Content;

class Contact_Page extends \Tribe\Libs\Required_Page\Required_Page {
	public const NAME = 'contact_page';

	protected function get_title() {
		return _x( 'Contact Us', 'contact page title', 'tribe' );
	}

	protected function get_slug() {
		return _x( 'contact', 'contact page slug', 'tribe' );
	}
}
```

Then register it in a Definer:

```php
<?php

namespace Tribe\Example\Content;

use Psr\Container\ContainerInterface;
use Tribe\Libs\Container\Definer_Interface;
use \Tribe\Libs\Required_Page\Required_Page_Definer;

class Content_Definer implements Definer_Interface {
	public function define(): array {
		return [
			// add the page to the existing array
			Required_Page_Definer::PAGES => \DI\add( [
				\DI\get( Contact_Page::class ),
			] ),

			// register the contact page with the key for the settings group we want this setting to appear in
			Contact_Page::class      => static function ( ContainerInterface $container ) {
				return new Contact_Page( $container->get( \Tribe\Example\Object_Meta\Example::class )->get_group_config()['key'] );
			},
		];
	}
}
```
