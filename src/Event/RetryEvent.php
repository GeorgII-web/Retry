<?php

declare(strict_types=1);

namespace GeorgII\Event;

use InvalidArgumentException;
use Throwable;

use function in_array;

/**
 * @psalm-immutable
 */
final readonly class RetryEvent
{
    public const ON_BEFORE_TRY = 'onBeforeTry';
    public const ON_AFTER_TRY = 'onAfterTry';
    public const ON_NOT_RETRIABLE_FAIL = 'onNotRetriableFail';
    public const ON_LAST_ATTEMPT_FAIL = 'onLastAttemptFail';
    public const ON_NEXT_TRY = 'onNextTry';

    /**
     * @param non-empty-string $name
     * @param positive-int     $attempt
     * @param positive-int     $retryCount
     */
    public function __construct(
        private string $name,
        private int $attempt,
        private int $retryCount,
        private float $delay,
        private ?Throwable $exception,
    ) {
        if (empty($name)) {
            throw new InvalidArgumentException('Name cannot be empty');
        }

        if (!in_array($name, [
            self::ON_BEFORE_TRY,
            self::ON_AFTER_TRY,
            self::ON_NOT_RETRIABLE_FAIL,
            self::ON_LAST_ATTEMPT_FAIL,
            self::ON_NEXT_TRY,
        ])) {
            throw new InvalidArgumentException('The name must be one of the predefined constants');
        }

        if ($attempt <= 0) {
            throw new InvalidArgumentException('Attempt must be greater than zero');
        }

        if ($retryCount <= 0) {
            throw new InvalidArgumentException('RetryCount must be greater than zero');
        }

        if ($delay <= 0.0) {
            throw new InvalidArgumentException('Delay must be greater than zero');
        }
    }

    /**
     * @psalm-suppress PossiblyUnusedMethod
     *
     * @return non-empty-string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @psalm-suppress PossiblyUnusedMethod
     *
     * @return positive-int
     */
    public function getAttempt(): int
    {
        return $this->attempt;
    }

    /**
     * @psalm-suppress PossiblyUnusedMethod
     *
     * @return positive-int
     */
    public function getRetryCount(): int
    {
        return $this->retryCount;
    }

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function getDelay(): float
    {
        return $this->delay;
    }

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function getException(): ?Throwable
    {
        return $this->exception;
    }
}
