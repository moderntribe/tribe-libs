# Pipeline

This is a customized implementation of Laravel's [illuminate/pipeline](https://github.com/illuminate/pipeline) to work with PHP-DI and to allow passing additional parameters to your stages.

This library implements a version of the [Chain of Responsibility Design Pattern](https://refactoring.guru/design-patterns/chain-of-responsibility). This system is what powers [Laravel's Middleware](https://laravel.com/docs/8.x/middleware) under the hood and is very useful if you want to run multiple operations in a sequence on the same data set.

See the [tests](/tests/integration/Tribe/Libs/Pipeline/PipelineTest.php) for example usage.

## TODO: Provide Examples
