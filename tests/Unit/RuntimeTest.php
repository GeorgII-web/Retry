<?php

declare(strict_types=1);

use GeorgII\Event\RetryEvent;
use GeorgII\Retry;

it('tries 3 times and fail', function (): void {
    $attemptCount = 0;
    $failed = false;

    try {
        Retry::onAnyException(
            attemptCallback: function (RetryEvent $event) use (&$attemptCount): void {
                $attemptCount = $event->getAttempt();
                throw new RuntimeException('Fail');
            },
            retryCount: 3,
            retryDelay: 0.001,
            eventCallback: function (RetryEvent $event): void {
            }
        );
    } catch (Exception $e) {
        $failed = true;
    }

    expect($attemptCount)->toBe(3)
        ->and($failed)->toBeTrue();
});

it('tries 1 time and success', function (): void {
    $attemptCount = 0;
    $failed = false;

    try {
        Retry::onAnyException(function (RetryEvent $event) use (&$attemptCount): void {
            $attemptCount = $event->getAttempt();
        }, 3, 0.001);
    } catch (Exception $e) {
        $failed = true;
    }

    expect($attemptCount)->toBe(1)
        ->and($failed)->toBeFalse();
});

it('tries 1 time and success on exact exception', function (): void {
    $attemptCount = 0;
    $failed = false;

    try {
        Retry::onExactExceptions(function (RetryEvent $event) use (&$attemptCount): void {
            $attemptCount = $event->getAttempt();
        },
            [Exception::class],
            3,
            0.001
        );
    } catch (Exception $e) {
        $failed = true;
    }

    expect($attemptCount)->toBe(1)
        ->and($failed)->toBeFalse();
});

it('tries 1 time and success on related exception', function (): void {
    $attemptCount = 0;
    $failed = false;

    try {
        Retry::onRelatedExceptions(function (RetryEvent $event) use (&$attemptCount): void {
            $attemptCount = $event->getAttempt();
        },
            [Exception::class],
            3,
            0.001
        );
    } catch (Exception $e) {
        $failed = true;
    }

    expect($attemptCount)->toBe(1)
        ->and($failed)->toBeFalse();
});
