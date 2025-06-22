<?php

declare(strict_types=1);

namespace GeorgII\Example;

use GeorgII\Event\RetryEvent;
use GeorgII\Retry;
use RuntimeException;

/**
 * @psalm-suppress UnusedClass
 *
 * @codeCoverageIgnoreStart
 */
final class SimpleExample
{
    public function simpleExample(): void
    {
        Retry::onAnyException(function (RetryEvent $event): void {
            echo $event->getAttempt();
            throw new RuntimeException('test');
        });
    }

    public function simpleWithRetryCountExample(): void
    {
        Retry::onAnyException(function (RetryEvent $event): void {
            echo $event->getAttempt();
            throw new RuntimeException('test');
        }, retryCount: 2);
    }

    public function simpleWithRetryCountAndDelayExample(): void
    {
        Retry::onAnyException(function (RetryEvent $event): void {
            echo $event->getAttempt();
            throw new RuntimeException('test');
        }, retryCount: 2, retryDelay: 0.1);
    }

    public function simpleWithRetryCountDelayAndEventListenerExample(): void
    {
        Retry::onAnyException(
            attemptCallback: function (RetryEvent $event): void {
                echo $event->getAttempt();
                throw new RuntimeException('test');
            },
            retryCount: 2,
            retryDelay: 0.1,
            eventCallback: function (RetryEvent $event): void {
                echo $event->getAttempt();
            }
        );
    }
}
/**
 * @codeCoverageIgnoreEnd
 */
