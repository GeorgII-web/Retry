<?php

declare(strict_types=1);

namespace GeorgII;

use Closure;
use Exception;
use GeorgII\Event\RetryEvent;
use GeorgII\Event\RetryEventFactory;
use GeorgII\Retry\RetryCore;

/**
 * @template T
 *
 * Retry ALIASES for executing callbacks with retry logic, handling specific
 * retryable exceptions, and customizable delay/retry counts.
 *
 * Simple usage example:
 * ```
 * Retry::onAnyException(function (RetryEvent $event): void {
 *      dump($event);
 *      throw new Exception('test');
 * }, retryCount: 2, retryDelay: 0.1);
 * ```
 */
class Retry
{
    /**
     * Retry on ANY exception alias.
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
    public static function onAnyException(
        Closure $attemptCallback,
        ?int $retryCount = null,
        int|float|Closure|null $retryDelay = null,
        ?Closure $eventCallback = null,
    ): mixed {
        $retry = new RetryCore();

        return $retry
            ->setRetryExceptions([
                Exception::class,
            ])
            ->setEventCallback($eventCallback)
            ->setRetryCount($retryCount)
            ->setRetryDelay($retryDelay)
            ->setEventFactory(new RetryEventFactory($retry))
            ->setCheckExactException(false)
            ->handle($attemptCallback);
    }

    /**
     * Retry on RELATED exceptions alias.
     * Defined Exceptions will be treated as parents for thrown Exceptions.
     * For example, if LogicException is defined, InvalidArgumentException will be retried.
     *
     * @template R
     *
     * @param Closure(RetryEvent): R          $attemptCallback
     * @param array<non-empty-string>         $exceptions
     * @param positive-int|null               $retryCount
     * @param positive-int|float|Closure|null $retryDelay
     * @param Closure(RetryEvent):void|null   $eventCallback
     *
     * @return R
     */
    public static function onRelatedExceptions(
        Closure $attemptCallback,
        array $exceptions,
        ?int $retryCount = null,
        int|float|Closure|null $retryDelay = null,
        ?Closure $eventCallback = null,
    ): mixed {
        $retry = new RetryCore();

        return $retry
            ->setRetryExceptions($exceptions)
            ->setEventCallback($eventCallback)
            ->setRetryCount($retryCount)
            ->setRetryDelay($retryDelay)
            ->setEventFactory(new RetryEventFactory($retry))
            ->setCheckExactException(false)
            ->handle($attemptCallback);
    }

    /**
     * Retry on EXACT exceptions alias.
     * Only defined Exceptions will be retried.
     * For example, if LogicException is defined, InvalidArgumentException will be ignored.
     *
     * @template R
     *
     * @param Closure(RetryEvent): R          $attemptCallback
     * @param array<non-empty-string>         $exceptions
     * @param positive-int|null               $retryCount
     * @param positive-int|float|Closure|null $retryDelay
     * @param Closure(RetryEvent):void|null   $eventCallback
     *
     * @return R
     */
    public static function onExactExceptions(
        Closure $attemptCallback,
        array $exceptions,
        ?int $retryCount = null,
        int|float|Closure|null $retryDelay = null,
        ?Closure $eventCallback = null,
    ): mixed {
        $retry = new RetryCore();

        return $retry
            ->setRetryExceptions($exceptions)
            ->setEventCallback($eventCallback)
            ->setRetryCount($retryCount)
            ->setRetryDelay($retryDelay)
            ->setEventFactory(new RetryEventFactory($retry))
            ->setCheckExactException(true)
            ->handle($attemptCallback);
    }
}
