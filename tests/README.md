# Tests for Tribe Libs

## Setup Instructions

1. Run `composer install`.
2. Run `composer test:setup` to download and configure test dependencies.
3. If appropriate, edit [.env](.env) to update environment variables to match your local environment.
4. Create the `tribe_libs_test` database.
5. Run `composer test:unit` to run unit tests.
6. Run `composer test:integration` to run integration tests.
7. Run `composer -- test:integration --env multisite` to run multisite specific integration tests.
