<?php

declare(strict_types=1);

use GeorgII\Event\RetryEventFactory;
use GeorgII\Retry\RetryCore;

beforeEach(function (): void {
    Mockery::globalHelpers();
});

afterEach(function (): void {
    Mockery::close();
});

it('can set and get retry count', function (): void {
    $retryCore = new RetryCore();
    $retryCore->setRetryCount(5);
    expect($retryCore->getRetryCount())->toBe(5);
});

it('throws an exception if retry count is set to zero', function (): void {
    $retryCore = new RetryCore();
    $retryCore->setRetryCount(0);
})->throws(InvalidArgumentException::class, 'Retry count must be greater than zero.');

it('can set and get retry delay', function (): void {
    $retryCore = new RetryCore();
    $retryCore->setRetryDelay(2.5);
    expect($retryCore->getRetryDelay(1))->toBe(2.5);

    $retryCore->setRetryDelay(fn($attempt) => $attempt * 0.5);
    expect($retryCore->getRetryDelay(3))->toBe(1.5);
});

it('validates exception classes when setting retry exceptions', function (): void {
    $retryCore = new RetryCore();

    $retryCore->setRetryExceptions([InvalidArgumentException::class]);
    expect(true)->toBeTrue(); // If no exception is thrown, the test passes

    $retryCore->setRetryExceptions(['NonExistentClass']);
})->throws(InvalidArgumentException::class, 'Class "NonExistentClass" does not exist.');

it('throws LogicException if retry logic ends unexpectedly', function (): void {
    $retryCore = new class extends RetryCore {
        protected function isRetriableException(Throwable $e): bool
        {
            return false; // Force failure to trigger an unexpected end scenario
        }
    };

    $retryCore->setEventFactory(new RetryEventFactory($retryCore));
    $retryCore->setRetryCount(3);

    $retryCore->handle(fn() => throw new LogicException('Will fail early'));
})->throws(LogicException::class, 'Will fail early');

it('throws an exception if event factory is not set', function (): void {
    $class = new RetryCore();
    $class->getEventFactory();
})->throws(InvalidArgumentException::class, 'Event factory is not set');

it('throws an exception if a class in the exceptions array does not exist', function (): void {
    $class = new RetryCore();

    $class->setRetryExceptions(['NonExistentClass']);
})->throws(InvalidArgumentException::class, 'Class "NonExistentClass" does not exist.');

it('throws an exception if a class in the exceptions array is not a subclass of Throwable', function (): void {
    $class = new RetryCore();

    $class->setRetryExceptions([stdClass::class]); // stdClass is not a subclass of Throwable
})->throws(InvalidArgumentException::class, 'Class "stdClass" is not a valid exception (not a subclass of Throwable).');
