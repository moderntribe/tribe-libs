# Tests for Tribe Libs

Requires **PHP 8.1+**.

## Suites

| Command | Suite | What it covers |
|---------|-------|----------------|
| `composer test:unit` | unit | Fast Brain Monkey unit tests (no WordPress bootstrap) |
| `composer test:integration` | integration | WordPress-loaded integration tests |
| `composer test:functional` | integration | Alias of integration (WP functional behaviour) |
| `composer test:all` | unit + integration | Full local run |

There is **no browser acceptance suite** in this library. Plugin-level acceptance belongs in the consuming project.

## Local setup

1. `composer install` (or `lando composer install`)
2. `composer test:setup` — downloads WordPress + test plugins into `tests/`
3. For Lando: copy `tests/.env-lando` → `tests/.env` (already done by typical setup), then create the test DB:

```bash
lando mysql -e "CREATE DATABASE IF NOT EXISTS tribe_libs_test; GRANT ALL PRIVILEGES ON tribe_libs_test.* TO 'wordpress'@'%'; FLUSH PRIVILEGES;"
```

4. Without Lando: copy `tests/.env-dist` → `tests/.env` and create `tribe_libs_test` for the configured MySQL user
5. Run tests:

```bash
composer test:unit
composer test:integration
# or
composer test:all
```

Multisite:

```bash
composer -- test:integration --env multisite
```
