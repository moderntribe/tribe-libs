# Tests for Tribe Libs

## Setup

1. Download WordPress into the `wordpress` subdirectory in the root of this repository
2. Download and extract the [Posts to Posts plugin](https://downloads.wordpress.org/plugin/posts-to-posts.1.6.5.zip) to the `wp-content/plugins` folder.
3. Copy `.github/config/wp-config-environment.php` to the root of this repository
4. Copy `tests/.env-dist` to `tests/.env`
5. Create the database `tribe_libs_test`
6. Run `composer install`

Change any environment variables in `.env` to match your local environment, if appropriate

## Run

1. Run `vendor/bin/codecept --config ./tests run integration`
