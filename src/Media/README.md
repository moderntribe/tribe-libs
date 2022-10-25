# SquareOne Media Utilities

A collection of utilities to handle typical media filters in SquareOne projects.

## Full Size GIFs

When WordPress resizes images, animates GIFs are no longer animated. This utility encourages
WordPress to use the full size version of GIF images.

Disable this feature by setting a constant in your `wp-config.php` (by default, it is enabled).

```php
define( 'FORCE_FULL_SIZE_GIFS', false ); // disables the full size GIF filter
```

## Responsive Image Disabler

We handle responsive images in our own special way. WordPress doesn't play well with our technique,
so we disable its responsive image filters.

Disable this feature by setting a constant in your `wp-config.php` (by default, it is enabled).

```php
define( 'DISABLE_WP_RESPONSIVE_IMAGES', false ); // disables the responsive images disabler
```

## SVG Markup Storage

When new SVGs are uploaded/modified, their markup is saved to post meta so future file reads aren't required to get the SVG markup.

Reading file contents is slower than a database record and any projects using Cloud/Remote Based File Systems, such as s3 won't need to make remote connections and will only slow down during the initial upload where parsing and storage will take place.

> **Tip:** SVG markup is only stored when an SVG attachment is uploaded to the media library. To store SVG attachment markup that was uploaded before this feature was active, run the CLI command: `wp s1 svg store --task=add`.

### Fetching stored SVG markup

Simply auto-inject the `Svg_Store` interface into your controller, and fetch the markup via `$attachment_id`. 

```php
<?php declare(strict_types=1);

namespace Tribe\Project\Controllers;

use Tribe\Libs\Media\Svg\Store\Contracts\Svg_Store

class My_Controller {
    
    protected Svg_Store $svg_store;
    
    public function __construct( Svg_Store $svg_store ) {
        $this->svg_store = $svg_store;
    }
    
    public function get_inline_logo(): string {
        // Image/Attachment ID fetched from settings/featured image etc...
        $image_id = 10;
        
        // Return the sanitized SVG markup.
        return $this->svg_store->get( $image_id );
    }
    
}
```

### Disable SVG storage system

With the following define, **newly uploaded SVGs** will no longer have their markup stored in post meta. Existing SVG meta will still remain in the database.

```php
define( 'TRIBE_ENABLE_SVG_INLINE_STORAGE', false );
```

> **Tip:** run the CLI command `wp s1 svg store --task=remove` to delete all existing SVG markup meta keys.
