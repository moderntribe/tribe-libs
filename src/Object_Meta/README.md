# Tribe Libs Object Meta

## Adding Meta Groups

### Create the `Meta_Group`

```php
<?php

namespace Tribe\Example\Object_Meta;

use Tribe\Libs\ACF;

class Example extends ACF\ACF_Meta_Group {

	public const NAME = 'example_meta';

	public const ONE = 'example_object_meta_one';
	public const TWO = 'example_object_meta_two';

	public function get_keys() {
		return [
			self::ONE,
			self::TWO,
		];
	}

	public function get_group_config() {
		$group = new ACF\Group( self::NAME, $this->object_types );
		$group->set( 'title', __( 'Example Object Meta', 'tribe' ) );

		$group->add_field( $this->get_field_one() );
		$group->add_field( $this->get_field_two() );

		return $group->get_attributes();
	}

	private function get_field_one() {
		$field = new ACF\Field( self::NAME . '_' . self::ONE );
		$field->set_attributes( [
			'label' => __( 'Example Object Meta #1', 'tribe' ),
			'name'  => self::ONE,
			'type'  => 'text',
		] );

		return $field;
	}

	private function get_field_two() {
		$field = new ACF\Field( self::NAME . '_' . self::TWO );
		$field->set_attributes( [
			'label' => __( 'Example Object Meta #2', 'tribe' ),
			'name'  => self::TWO,
			'type'  => 'text',
		] );

		return $field;
	}

}
```

### Register in the Definer

```php
<?php
declare( strict_types=1 );

namespace Tribe\Example\Object_Meta;

use DI;
use Psr\Container\ContainerInterface;
use Tribe\Libs\Container\Definer_Interface;
use Tribe\Example\Settings;

class Object_Meta_Definer implements Definer_Interface {
	public const GROUPS = 'meta.groups';

	public function define(): array {
		return [
			\Tribe\Libs\Object_Meta\Object_Meta_Definer::GROUPS => DI\add( [
				DI\get( Example::class ),
			] ),

			Example::class => static function ( ContainerInterface $container ) {
				return new Example( [ // use one or more of the following options
					// can be added to post types
					'post_types'     => [ 'page', 'post' ],
					// or can be added to taxonomies
					'taxonomies'     => [ 'category' ],
					// or can be added to settings screens
					'settings_pages' => [ $container->get( Settings\General::class )->get_slug() ],
					// or can be added to user profiles
					'users'          => true,
				] );
			},
		];
	}
}

```
