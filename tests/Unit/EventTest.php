<?php

declare(strict_types=1);

use GeorgII\Event\RetryEvent;

it('can create an instance of Event', function (): void {
    $event = new RetryEvent(
        name: 'onBeforeTry',
        attempt: 1,
        retryCount: 3,
        delay: 0.5,
        exception: null
    );

    expect($event)->toBeInstanceOf(RetryEvent::class);
});

it('can get the name from Event', function (): void {
    $event = new RetryEvent(
        name: 'onBeforeTry',
        attempt: 1,
        retryCount: 3,
        delay: 0.5,
        exception: null
    );

    expect($event->getName())->toBe('onBeforeTry');
});

it('can get the attempt count from Event', function (): void {
    $event = new RetryEvent(
        name: 'onBeforeTry',
        attempt: 1,
        retryCount: 3,
        delay: 0.5,
        exception: null
    );

    expect($event->getAttempt())->toBe(1);
});

it('can get the retry count from Event', function (): void {
    $event = new RetryEvent(
        name: 'onBeforeTry',
        attempt: 1,
        retryCount: 3,
        delay: 0.5,
        exception: null
    );

    expect($event->getRetryCount())->toBe(3);
});

it('can get the delay value from Event', function (): void {
    $event = new RetryEvent(
        name: 'onBeforeTry',
        attempt: 1,
        retryCount: 3,
        delay: 0.5,
        exception: null
    );

    expect($event->getDelay())->toBe(0.5);
});

it('can get the exception from Event', function (): void {
    $exception = new RuntimeException('Test Exception');

    $event = new RetryEvent(
        name: 'onBeforeTry',
        attempt: 1,
        retryCount: 3,
        delay: 0.5,
        exception: $exception
    );

    expect($event->getException())->toBe($exception);
});

it('returns null if no exception is provided', function (): void {
    $event = new RetryEvent(
        name: 'onBeforeTry',
        attempt: 1,
        retryCount: 3,
        delay: 0.5,
        exception: null
    );

    expect($event->getException())->toBeNull();
});

it('throws an exception if the name is empty', function (): void {
    new RetryEvent('', 1, 1, 1.0, null);
})->throws(InvalidArgumentException::class, 'Name cannot be empty');

it('throws an exception if the name is not predefined constant', function (): void {
    new RetryEvent('onRandomEvent', 1, 1, 1.0, null);
})->throws(InvalidArgumentException::class, 'The name must be one of the predefined constants');

it('throws an exception if the attempt is not positive', function (): void {
    new RetryEvent('onBeforeTry', 0, 1, 1.0, null);
})->throws(InvalidArgumentException::class, 'Attempt must be greater than zero');

it('throws an exception if the retryCount is not positive', function (): void {
    new RetryEvent('onBeforeTry', 1, 0, 1.0, null);
})->throws(InvalidArgumentException::class, 'RetryCount must be greater than zero');

it('throws an exception if the delay is not greater than 0', function (): void {
    new RetryEvent('onBeforeTry', 1, 1, 0.0, null);
})->throws(InvalidArgumentException::class, 'Delay must be greater than zero');
