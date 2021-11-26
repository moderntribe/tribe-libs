# Pipeline

This is a customized implementation of Laravel's [illuminate/pipeline](https://github.com/illuminate/pipeline) to work with PHP-DI and to allow passing additional parameters to your stages.

This library implements a version of the [Chain of Responsibility Design Pattern](https://refactoring.guru/design-patterns/chain-of-responsibility). This system is what powers [Laravel's Middleware](https://laravel.com/docs/8.x/middleware) under the hood and is very useful if you want to run multiple operations in a sequence on the same data set.

Each stage can perform a specific operation, or decide to skip that operation based on some condition and pass to the next stage in line.

See the [tests](/tests/integration/Tribe/Libs/Pipeline/PipelineTest.php) for some basic example use cases.

## Example Use Cases

1. As request/response middleware.
2. As a filter/facet system, e.g. published, sort etc...
3. As a permissions' system, allowing access by role or a number of different conditions.
4. As a formatting/transforming/data mapping system.
5. As a sanitizing system, performing different sanitization based on the structure of the data.
