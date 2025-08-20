<?php

declare(strict_types=1);

namespace GeorgII\Retry;

use Closure;
use Exception;
use GeorgII\Event\RetryEvent;
use GeorgII\Event\RetryEventFactory;
use GeorgII\Util\Sleep;
use InvalidArgumentException;
use LogicException;
use Throwable;

use function in_array;
use function is_callable;
use function sprintf;

/**
 * @template T
 *
 * Retry class for executing callbacks with retry logic, handling specific
 * retryable exceptions, and customizable delay/retry counts.
 */
class RetryCore
{
    private int $retryCount = 3;
    private Closure|int|float $retryDelay = .5;
    private ?Closure $eventCallback = null;
    private array $exceptions = [Throwable::class];
    private ?RetryEventFactory $eventFactory = null;
    private bool $checkExactException = true;

    public function setCheckExactException(bool $checkExactException): self
    {
        $this->checkExactException = $checkExactException;

        return $this;
    }

    public function isCheckExactException(): bool
    {
        return $this->checkExactException;
    }

    public function getEventFactory(): RetryEventFactory
    {
        if ($this->eventFactory === null) {
            throw new InvalidArgumentException('Event factory is not set');
        }

        return $this->eventFactory;
    }

    public function setEventFactory(RetryEventFactory $eventFactory): self
    {
        $this->eventFactory = $eventFactory;

        return $this;
    }

    public function setEventCallback(?Closure $eventCallback): self
    {
        $this->eventCallback = $eventCallback;

        return $this;
    }

    public function getEventCallback(): ?Closure
    {
        return $this->eventCallback;
    }

    /**
     * @param int|null $retryCount If null, will take a default value
     *
     * @return $this
     */
    public function setRetryCount(?int $retryCount): self
    {
        if ($retryCount > 0) {
            $this->retryCount = $retryCount;
        }

        if ($retryCount === 0) {
            throw new InvalidArgumentException('Retry count must be greater than zero.');
        }

        return $this;
    }

    /**
     * @return positive-int
     */
    public function getRetryCount(): int
    {
        return max(1, $this->retryCount);
    }

    public function setRetryDelay(int|float|Closure|null $retryDelay = null): self
    {
        if ($retryDelay !== null) {
            $this->retryDelay = $retryDelay;
        }

        return $this;
    }

    /**
     * @param positive-int $attempt
     */
    public function getRetryDelay(int $attempt): float
    {
        if (is_callable($this->retryDelay)) {
            return (float) ($this->retryDelay)($attempt);
        }

        return (float) $this->retryDelay;
    }

    /**
     * @param non-empty-string[] $exceptions Array of exception class names as strings
     */
    public function setRetryExceptions(array $exceptions): self
    {
        foreach ($exceptions as $exception) {
            // Check if the provided class exists
            if (!class_exists($exception)) {
                throw new InvalidArgumentException(sprintf('Class "%s" does not exist.', $exception));
            }

            // Check if the class is a subclass of Throwable
            if (!is_subclass_of($exception, Throwable::class)) {
                throw new InvalidArgumentException(sprintf('Class "%s" is not a valid exception (not a subclass of Throwable).', $exception));
            }
        }

        $this->exceptions = $exceptions;

        return $this;
    }

    /**
     * Run the provided callback with retry logic.
     *
     * @param callable(RetryEvent): T $callback
     *
     * @return T
     */
    public function handle(callable $callback): mixed
    {
        for ($attempt = 1; $attempt <= $this->retryCount; ++$attempt) {
            try {
                // Send event before callback.
                $event = $this->getEventFactory()->create(RetryEvent::ON_BEFORE_TRY, $attempt, null);
                $this->executeEventCallback($event);

                /**
                 * Execute the callback itself.
                 */
                $callbackResult = $callback($event);

                // Send event after callback
                $this->executeEventCallback(
                    $this->getEventFactory()->create(RetryEvent::ON_AFTER_TRY, $attempt, null)
                );

                return $callbackResult;
            } catch (Exception $e) {
                // Check if the exception is retryable
                if (!$this->isRetriableException($e)) {
                    // Execute not retriable event callback
                    $this->executeEventCallback(
                        $this->getEventFactory()->create(RetryEvent::ON_NOT_RETRIABLE_FAIL, $attempt, $e)
                    );

                    throw $e;
                }

                // If it's the last attempt, re-throw the exception
                if ($attempt === $this->retryCount) {
                    // Execute last attempt event callback
                    $this->executeEventCallback(
                        $this->getEventFactory()->create(RetryEvent::ON_LAST_ATTEMPT_FAIL, $attempt, $e)
                    );
                    throw $e;
                }

                // Execute the next try event callback
                $this->executeEventCallback(
                    $this->getEventFactory()->create(RetryEvent::ON_NEXT_TRY, $attempt, $e)
                );

                // Retry delay
                Sleep::delay(
                    $this->getRetryDelay($attempt)
                );
            }
        }

        throw new LogicException('Unexpected end of the retry loop');
    }

    /**
     * Validate if the exception can be retried.
     */
    private function isRetriableException(Throwable $e): bool
    {
        // Exact match: only the exact class names listed are retriable
        if ($this->isCheckExactException()) {
            return in_array($e::class, $this->exceptions, true);
        }

        // Related match: allow parents/interfaces without instantiating them
        foreach ($this->exceptions as $exceptionClass) {
            if (is_a($e, $exceptionClass)) {
                return true;
            }
        }

        return false;
    }

    private function executeEventCallback(RetryEvent $event): void
    {
        $this->getEventCallback()?->__invoke($event);
    }
}
