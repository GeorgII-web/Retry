# Retry

A PHP package to wrap any piece of code with a retry algorithm. It provides a simple and flexible way to handle transient failures in your code by automatically retrying operations that fail due to exceptions.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/georgii-web/retry.svg?style=flat-square)](https://packagist.org/packages/georgii-web/retry)
[![Tests](https://github.com/georgii-web/retry/actions/workflows/php.yml/badge.svg)](https://github.com/georgii-web/retry/actions/workflows/php.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/georgii-web/retry.svg?style=flat-square)](https://packagist.org/packages/georgii-web/retry)

## Requirements

- PHP 8.2 or higher

## Installation

You can install the package via composer:

```bash
composer require georgii-web/retry
```

## Basic Usage

The most basic way to use the package is to retry a piece of code a specific number of times:

```php
use GeorgII\Retry;

// Retry a function 3 times on any exception
$result = Retry::onAnyException(fn() => someOperation());
```

Some possible options:

```php
use GeorgII\Retry;
use GeorgII\RetryEvent;

// Retry a function 2 times, with a small delay and logging events on any exception
$result = Retry::onAnyException(
    attemptCallback: function (RetryEvent $event) {
        // Your code that might fail
       return someOperation($event);
    },
    retryCount: 2,
    retryDelay: 0.1,
    eventCallback: function (RetryEvent $event) {
        var_dump($event->getName());
    }
);
```
**Warning:** Only temporary exceptions should be retried. It is not meaningful to retry exceptions such as `Throwable`, validation exceptions, or any other issues that are unlikely to be resolved on subsequent attempts. Retrying should be reserved for transient errors, such as connection issues, where a retry has a realistic chance of succeeding.

## Custom aliases

To simplify the definition of specific retry logic, you can utilize an alias.
If the default retry logic is not sufficient for your needs, 
define your own in the project (e.g., specifying delays and exceptions to retry), 
wrap it in an alias, and use it in a straightforward manner.

```php
    /**
     * Retry on DB exception alias.
     *
     * @template R
     *
     * @param Closure(RetryEvent): R          $attemptCallback
     * @param positive-int|null               $retryCount
     * @param positive-int|float|Closure|null $retryDelay
     * @param Closure(RetryEvent):void|null   $eventCallback
     *
     * @return R
     */
    public static function onDbException(
        Closure $attemptCallback,
        ?int $retryCount = null,
        int|float|Closure|null $retryDelay = null,
        ?Closure $eventCallback = null,
    ): mixed {
        $retry = new RetryCore();

        return $retry
            ->setRetryExceptions([
                ConnectionLost::class,
            ])
            ->setEventCallback($eventCallback)
            ->setRetryCount($retryCount)
            ->setRetryDelay($retryDelay)
            ->setEventFactory(new RetryEventFactory($retry))
            ->setCheckExactException(false)
            ->handle($attemptCallback);
    }
```

Alias usage:

```php
$users = Retry::onDbException(fn() => $sql->query('SELECT * from users'));
```

## Documentation

For detailed usage examples and API reference, please see the [Documentation](DOCUMENTATION.md).

## Development

For information on setting up the development environment and contributing to the project, please see the [Development Guide](DEVELOPMENT.md).

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
