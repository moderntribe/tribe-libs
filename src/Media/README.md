# SquareOne Media Utilities

A collection of utilities to handle typical media filters in SquareOne projects.

## Full Size GIFs

When WordPress resizes images, animates GIFs are no longer animated. This utility encourages
WordPress to use the full size version of GIF images.

Disable this feature by setting a constant in your `wp-config.php` (by default, it is enabled).

```
define( 'FORCE_FULL_SIZE_GIFS', false ); // disables the full size GIF filter
```

## Responsive Image Disabler

We handle responsive images in our own special way. WordPress doesn't play well with our technique,
so we disable its responsive image filters.

Disable this feature by setting a constant in your `wp-config.php` (by default, it is enabled).

```
define( 'DISABLE_WP_RESPONSIVE_IMAGES', false ); // disables the responsive images disabler
```
