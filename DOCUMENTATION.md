# Retry Documentation

This document provides documentation and examples for using the Retry package.

More examples can be found in the `src/Examples` directory.

## Usage Examples

### Simple Example

The most basic way to use the package is to retry a piece of code a specific number of times:

```php
use GeorgII\Retry;

// Retry a function up to 3 times on any exception
$result = Retry::onAnyException(function () {
    // Your code that might fail
    return someOperation();
});

// Using arrow function syntax
$result = Retry::onAnyException(fn() => someOperation());
```

### Handling Specific Exceptions

You can specify which exceptions should trigger a retry:

```php
use GeorgII\Retry;
use App\Exceptions\TemporaryException;
use App\Exceptions\NetworkException;

// Retry only on specific exceptions
$result = Retry::onExactExceptions(
    function (RetryEvent $event) {
        // Your code that might fail
        return someOperation($event);
    },
    [TemporaryException::class, NetworkException::class]
);

// Retry on specific exceptions and their subclasses
$result = Retry::onRelatedExceptions(
    function (RetryEvent $event) {
        // Your code that might fail
        return someOperation($event);
    },
    [TemporaryException::class, NetworkException::class]
);
```

**Note:** There's an important distinction between "exact exceptions" and "related exceptions":
- `onExactExceptions`: Only retries on the exact exception types specified
- `onRelatedExceptions`: Retries on the specified exceptions and their subclasses

### Configuring Retry Count and Delay

You can customize the number of retry attempts and the delay between retries:

```php
use GeorgII\Retry;

// Retry up to 5 times with a 1-second delay between attempts
$result = Retry::onAnyException(
    function () {
        // Your code that might fail
        return someOperation();
    },
    retryCount: 5,
    retryDelay: 1.0 // seconds
);

// Retry with exponential backoff
$result = Retry::onAnyException(
    function (RetryEvent $event) {
        // Your code that might fail
        return someOperation($event);
    },
    retryCount: 5,
    retryDelay: function (int $attempt) {
        return pow(2, $attempt - 1) * 0.1; // 0.1s, 0.2s, 0.4s, 0.8s, 1.6s
    }
);
```

### Using Event Callbacks

You can monitor the retry process by providing an event callback:

```php
use GeorgII\Retry;
use GeorgII\Event\RetryEvent;

$result = Retry::onAnyException(
    function (RetryEvent $event) {
        // Your code that might fail
        return someOperation($event);
    },
    retryCount: 3,
    retryDelay: 0.5,
    eventCallback: function (RetryEvent $event) {
        // Event types: onBeforeTry, onAfterTry, onNextTry, onNotRetriableFail, onLastAttemptFail
        $eventName = $event->getName();
        $attempt = $event->getAttempt();
        $retryCount = $event->getRetryCount();
        $delay = $event->getDelay();
        $exception = $event->getException();

        // Log or handle the event
        logger()->info("Retry event: {$eventName}, Attempt: {$attempt}/{$retryCount}, error {$exception->getMessage()}");
    }
);
```

Using events to interact with built-in steps:

```php
use GeorgII\Retry;
use GeorgII\Event\RetryEvent;

$result = Retry::onAnyException(fn() => someOperation($event), 3, 5,
    eventCallback: function (RetryEvent $event) {
        // Event types: onBeforeTry, onAfterTry, onNextTry, onNotRetriableFail, onLastAttemptFail
        $eventName = $event->getName();
        if ($eventName === 'onLastAttemptFail') {
            logger()->info("Do something after last failed attempt");
        }
    }
);
```

### Creating Custom Retry Aliases

You can create your own retry functions for specific use cases:

```php
use GeorgII\Retry;
use GeorgII\Event\RetryEvent;
use App\Exceptions\DatabaseException;

/**
 * @template R
 * @param callable(RetryEvent): R $attemptCallback
 * @return R
 */
function retryDatabaseOperation(callable $attemptCallback): mixed
{
    return Retry::onRelatedExceptions(
        $attemptCallback,
        [DatabaseException::class],
        3,
        function (int $attempt) {
            return pow(2, $attempt - 1) * 0.1; // Exponential backoff
        },
        function (RetryEvent $event) {
            logger()->info("Database retry: {$event->getName()}, Attempt: {$event->getAttempt()}");
        }
    );
}

// Usage of the custom retry function
$result = retryDatabaseOperation(fn() => $database->query(<<<SQL
    SELECT * FROM users
SQL));
```

### Advanced Configuration

For more advanced use cases, you can directly use the `RetryCore` class:

```php
use GeorgII\Retry\RetryCore;
use GeorgII\Event\RetryEventFactory;
use GeorgII\Event\RetryEvent;
use App\Exceptions\TemporaryException;

$retry = new RetryCore();
$retry->setRetryExceptions([TemporaryException::class]);
$retry->setRetryCount(5);
$retry->setRetryDelay(function (int $attempt) {
    return pow(2, $attempt - 1) * 0.1; // Exponential backoff
});
$retry->setEventCallback(function (RetryEvent $event) {
    logger()->info("Retry event: {$event->getName()}");
});
$retry->setEventFactory(new RetryEventFactory($retry));
$retry->setCheckExactException(false); // Include subclasses

$result = $retry->handle(function (RetryEvent $event) {
    // Your code that might fail
    return someOperation($event);
});
```

## API Reference

### Main Class: `Retry`

The `Retry` class provides static methods for common retry scenarios:

- `onAnyException(Closure(RetryEvent): R $attemptCallback, ?positive-int $retryCount = null, positive-int|float|Closure|null $retryDelay = null, ?Closure(RetryEvent): void $eventCallback = null): R`
  - Retries the callback on any exception

- `onRelatedExceptions(Closure(RetryEvent): R $attemptCallback, array<non-empty-string> $exceptions, ?positive-int $retryCount = null, positive-int|float|Closure|null $retryDelay = null, ?Closure(RetryEvent): void $eventCallback = null): R`
  - Retries the callback on specified exceptions and their subclasses

- `onExactExceptions(Closure(RetryEvent): R $attemptCallback, array<non-empty-string> $exceptions, ?positive-int $retryCount = null, positive-int|float|Closure|null $retryDelay = null, ?Closure(RetryEvent): void $eventCallback = null): R`
  - Retries the callback only on the exact specified exceptions

### Core Class: `RetryCore`

The `RetryCore` class provides the underlying retry functionality:

- `setRetryExceptions(array<non-empty-string> $exceptions): self` - Sets the exceptions to retry on
- `setRetryCount(?positive-int $retryCount): self` - Sets the number of retry attempts
- `setRetryDelay(positive-int|float|Closure|null $retryDelay): self` - Sets the delay between retries
- `setEventCallback(?Closure(RetryEvent): void $eventCallback): self` - Sets the event callback
- `setEventFactory(RetryEventFactory $eventFactory): self` - Sets the event factory
- `setCheckExactException(bool $checkExactException): self` - Sets whether to check for exact exceptions
- `handle(Closure(RetryEvent): R $callback): R` - Handles the retry logic

### Event Class: `RetryEvent`

The `RetryEvent` class provides information about the retry process:

- `getName(): string` - Gets the event name
- `getAttempt(): int` - Gets the current attempt number
- `getRetryCount(): int` - Gets the total retry count
- `getDelay(): float|int|null` - Gets the delay before the next retry
- `getException(): ?Exception` - Gets the exception that triggered the retry
