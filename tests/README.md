# Tests for Tribe Libs

## Setup

1. Create a clone of Square One and run a `composer install`
1. Follow the setup instructions for tests in Square One
1. Create an `.env` file in the same directory as this readme with correct values for connecting to your `tribe_square1_tests` database
1. Run `./dev/docker/exec.sh /application/www/dev/docker/codecept.sh -c /application/www/vendor/moderntribe/tribe-libs/tests/ run integration` for integration tests

