# Tests for Tribe Libs

## Setup

1. Download WordPress into the `wordpress` subdirectory in the root of this repository
1. Copy `.github/config/wp-config-environment.php` to the root of this repository
1. Copy `tests/.env-dist` to `tests/.env`
1. Create the database `tribe_libs_test`
1. Run `composer install`

Change any environment variables in `.env` to match your local environment, if appropriate

## Run

1. Run `vendor/bin/codecept --config ./tests run integration`
